<?php

namespace App\Http\Controllers;

use App\Models\DiscountCampaign;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\PointsTransaction;
use App\Models\ProductReview;
use App\Models\ReferralLink;
use App\Services\PaystackPayments;
use App\Notifications\PointsEarnedNotification;
use App\Notifications\RedeemableThresholdReachedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()->with('items.product')->latest()->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items.product', 'statusHistories.actor']);
        $userReviews = ProductReview::query()
            ->where('user_id', auth()->id())
            ->whereIn('product_id', $order->items->pluck('product_id'))
            ->get()
            ->keyBy('product_id');

        $canReviewOrder = in_array($order->status, ['confirmed', 'completed'], true);

        return view('orders.show', compact('order', 'userReviews', 'canReviewOrder'));
    }

    public function cancel(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        // Paid (confirmed) orders need a refund, so they go through support instead.
        abort_unless($order->status === 'pending', 422, 'Paid orders can only be cancelled by contacting PluggedIn support.');

        $order->cancelAndRestoreInventory(auth()->id(), $request->input('reason'));

        return redirect()->route('orders.show', $order)->with('success', 'Your order has been cancelled.');
    }

    public function checkout()
    {
        $cartItems = auth()->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        $autoCampaign = DiscountCampaign::active()
            ->whereNull('code')
            ->orderByDesc('discount_percent')
            ->first();

        $discountAmount = 0;
        $total = $subtotal;

        if ($autoCampaign) {
            $discountAmount = round(($subtotal * $autoCampaign->discount_percent) / 100, 2);
            $total = max(0, $subtotal - $discountAmount);
        }

        return view('orders.checkout', compact('cartItems', 'subtotal', 'total', 'autoCampaign', 'discountAmount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'fulfilment_method' => 'required|in:delivery,campus_pickup',
            'pickup_campus' => 'required_if:fulfilment_method,campus_pickup|nullable|string|max:120',
            'delivery_address' => 'required_if:fulfilment_method,delivery|nullable|string',
            'delivery_phone' => 'required|string',
            'promo_code' => 'nullable|string|max:100',
        ]);

        $cartItems = auth()->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $promoCode = $request->filled('promo_code') ? strtoupper(trim((string) $request->input('promo_code'))) : null;

        $order = DB::transaction(function() use ($request, $cartItems, $promoCode) {
            $subtotal = $cartItems->sum(function($item) {
                return $item->product->price * $item->quantity;
            });

            $campaign = null;
            $discountAmount = 0;

            if ($promoCode) {
                $campaign = DiscountCampaign::query()
                    ->whereRaw('UPPER(code) = ?', [$promoCode])
                    ->lockForUpdate()
                    ->first();

                if (! $campaign || ! $this->isCampaignCurrentlyActive($campaign)) {
                    throw ValidationException::withMessages([
                        'promo_code' => 'Promo code is invalid or not active.',
                    ]);
                }
            } else {
                $campaign = DiscountCampaign::active()
                    ->whereNull('code')
                    ->orderByDesc('discount_percent')
                    ->lockForUpdate()
                    ->first();
            }

            if ($campaign) {
                if ($campaign->max_uses !== null && $campaign->used_count >= $campaign->max_uses) {
                    throw ValidationException::withMessages([
                        'promo_code' => 'Promo code usage limit has been reached.',
                    ]);
                }

                $discountAmount = round(($subtotal * $campaign->discount_percent) / 100, 2);
            }

            $total = max(0, $subtotal - $discountAmount);

            $order = Order::create([
                'user_id' => auth()->id(),
                'total_price' => $total,
                'subtotal_price' => $subtotal,
                'discount_amount' => $discountAmount,
                'discount_campaign_id' => $campaign?->id,
                'applied_discount_code' => $campaign?->code,
                'status' => 'pending',
                'fulfilment_method' => $request->fulfilment_method,
                'pickup_campus' => $request->pickup_campus,
                'delivery_address' => $request->delivery_address,
                'delivery_phone' => $request->delivery_phone
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'changed_by' => auth()->id(),
                'from_status' => null,
                'to_status' => 'pending',
            ]);

            foreach ($cartItems as $cartItem) {
                $product = Product::query()->lockForUpdate()->find($cartItem->product_id);

                if (! $product || $product->stock_quantity < $cartItem->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => "Insufficient stock for {$cartItem->product->name}.",
                    ]);
                }

                $referrals = session('referrals', []);
                $referralLinkId = $referrals[$cartItem->product_id] ?? null;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'referral_link_id' => $referralLinkId,
                    'quantity' => $cartItem->quantity,
                    'price' => $product->price
                ]);

                $product->decrement('stock_quantity', $cartItem->quantity);
            }

            // Clear cart
            auth()->user()->cartItems()->delete();

            // Clear tracked referrals once checkout is complete.
            session()->forget('referrals');

            return $order;
        });

        $reference = 'PLUG-' . $order->id . '-' . Str::upper(Str::random(16));
        $paymentResponse = Http::withToken(config('services.paystack.secret_key'))
            ->acceptJson()
            ->post(rtrim(config('services.paystack.base_url'), '/') . '/transaction/initialize', [
                'amount' => (int) round(((float) $order->total_price) * 100),
                'email' => auth()->user()->email,
                'currency' => config('services.paystack.currency', 'GHS'),
                'reference' => $reference,
                'callback_url' => route('payments.paystack.callback'),
                'metadata' => ['order_id' => $order->id],
            ]);

        if (! $paymentResponse->successful() || ! $paymentResponse->json('status')) {
            $order->cancelAndRestoreInventory(auth()->id(), 'Paystack payment initialization failed.');

            throw ValidationException::withMessages([
                'payment' => 'We could not start the payment. Please try again.',
            ]);
        }

        PaymentTransaction::create([
            'order_id' => $order->id,
            'reference' => $reference,
            'access_code' => $paymentResponse->json('data.access_code'),
            'status' => 'pending',
            'amount' => $order->total_price,
            'currency' => config('services.paystack.currency', 'GHS'),
            'gateway_response' => $paymentResponse->json(),
        ]);

        return redirect()->away($paymentResponse->json('data.authorization_url'));
    }

    public function paystackCallback(Request $request)
    {
        $reference = $request->string('reference')->toString();
        abort_unless($reference !== '', 400, 'Payment reference is missing.');

        $payment = PaymentTransaction::where('reference', $reference)->firstOrFail();
        abort_unless($payment->order->user_id === auth()->id(), 403);

        app(PaystackPayments::class)->verifyAndComplete($reference);

        return redirect()->route('orders.show', $payment->order)->with('success', 'Payment confirmed. Your order is being prepared.');
    }

    public function paystackWebhook(Request $request)
    {
        $signature = $request->header('x-paystack-signature');
        abort_unless($signature && hash_equals(hash_hmac('sha512', $request->getContent(), config('services.paystack.secret_key')), $signature), 401);

        if ($request->input('event') === 'charge.success') {
            app(PaystackPayments::class)->verifyAndComplete((string) data_get($request->input('data'), 'reference'));
        }

        return response()->json(['received' => true]);
    }

    private function isCampaignCurrentlyActive(DiscountCampaign $campaign): bool
    {
        $now = now();

        if (! $campaign->is_active) {
            return false;
        }

        if ($campaign->starts_at && $campaign->starts_at->gt($now)) {
            return false;
        }

        if ($campaign->ends_at && $campaign->ends_at->lt($now)) {
            return false;
        }

        if ($campaign->max_uses !== null && $campaign->used_count >= $campaign->max_uses) {
            return false;
        }

        return true;
    }
}
