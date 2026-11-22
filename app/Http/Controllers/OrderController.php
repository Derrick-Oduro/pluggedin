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
    /** Status groups for the "My orders" tabs. */
    private const ORDER_FILTERS = [
        'active' => ['pending', 'confirmed', 'processing', 'ready_for_pickup', 'shipped'],
        'completed' => ['completed'],
        'cancelled' => ['cancelled', 'refunded'],
    ];

    public function index(Request $request)
    {
        $filter = array_key_exists($request->query('filter'), self::ORDER_FILTERS) ? $request->query('filter') : 'all';

        $counts = auth()->user()->orders()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $filterCounts = ['all' => (int) $counts->sum()];
        foreach (self::ORDER_FILTERS as $key => $statuses) {
            $filterCounts[$key] = (int) $counts->only($statuses)->sum();
        }

        $orders = auth()->user()->orders()
            ->with('items.product')
            ->when($filter !== 'all', fn ($query) => $query->whereIn('status', self::ORDER_FILTERS[$filter]))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', compact('orders', 'filter', 'filterCounts'));
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

        // Paid orders need a refund, so they go through support instead. Unpaid pay-on-delivery
        // orders can be cancelled by the buyer until PluggedIn starts preparing them.
        $cancellable = $order->status === 'pending' || ($order->status === 'confirmed' && $order->awaitingCash());
        abort_unless($cancellable, 422, 'This order can only be cancelled by contacting PluggedIn support.');

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

        $cashOnDelivery = $this->cashOnDeliveryAvailability();

        return view('orders.checkout', compact('cartItems', 'subtotal', 'total', 'autoCampaign', 'discountAmount', 'cashOnDelivery'));
    }

    /**
     * Whether this buyer may choose pay-on-delivery: ['allowed' => bool, 'reason' => ?string].
     * Each open (unpaid) pay-on-delivery order holds stock, so buyers get a small number at once.
     */
    private function cashOnDeliveryAvailability(): array
    {
        if (! config('marketplace.cash_on_delivery')) {
            return ['allowed' => false, 'reason' => null];
        }

        $limit = max(1, (int) config('marketplace.cash_on_delivery_open_limit', 2));
        $open = auth()->user()->orders()
            ->where('payment_method', Order::CASH_ON_DELIVERY)
            ->whereNull('paid_at')
            ->whereNotIn('status', ['cancelled', 'refunded'])
            ->count();

        return $open >= $limit
            ? ['allowed' => false, 'reason' => "You already have {$open} pay-on-delivery ".\Illuminate\Support\Str::plural('order', $open).' waiting. Pay for or receive those first, or pay this one online.']
            : ['allowed' => true, 'reason' => null];
    }

    public function store(Request $request)
    {
        $request->validate([
            'fulfilment_method' => 'required|in:delivery,campus_pickup',
            'pickup_campus' => 'required_if:fulfilment_method,campus_pickup|nullable|string|max:120',
            'delivery_address' => 'required_if:fulfilment_method,delivery|nullable|string',
            'delivery_phone' => 'required|string',
            'promo_code' => 'nullable|string|max:100',
            'payment_method' => 'nullable|in:'.Order::PAYSTACK.','.Order::CASH_ON_DELIVERY,
        ]);

        $cartItems = auth()->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $payOnDelivery = $request->input('payment_method') === Order::CASH_ON_DELIVERY;

        if ($payOnDelivery && ! ($cod = $this->cashOnDeliveryAvailability())['allowed']) {
            return redirect()->route('checkout')->withInput()->withErrors([
                'payment_method' => $cod['reason'] ?? 'Pay on delivery is not available right now. Please pay online.',
            ]);
        }

        $promoCode = $request->filled('promo_code') ? strtoupper(trim((string) $request->input('promo_code'))) : null;

        $order = DB::transaction(function() use ($request, $cartItems, $promoCode, $payOnDelivery) {
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
                'payment_method' => $payOnDelivery ? Order::CASH_ON_DELIVERY : Order::PAYSTACK,
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

                if (! $product || $product->status !== 'approved' || $product->listing_state !== 'active') {
                    throw ValidationException::withMessages([
                        'cart' => "{$cartItem->product->name} is no longer available. Remove it from your cart to continue.",
                    ]);
                }

                if ($product->stock_quantity < $cartItem->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => $product->stock_quantity > 0
                            ? "Only {$product->stock_quantity} of {$product->name} left. Lower the quantity in your cart to continue."
                            : "{$product->name} has just sold out. Remove it from your cart to continue.",
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

            if ($payOnDelivery) {
                // Nothing to wait for online: the order goes straight to PluggedIn to prepare,
                // and the money is collected on delivery or pickup (see the admin "Cash received" action).
                $order->update(['status' => 'confirmed']);
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'changed_by' => auth()->id(),
                    'from_status' => 'pending',
                    'to_status' => 'confirmed',
                ]);

                if ($campaign) {
                    $campaign->increment('used_count');
                }
            }

            return $order;
        });

        if ($payOnDelivery) {
            auth()->user()->cartItems()->delete();
            session()->forget('referrals');

            $when = $order->fulfilment_method === 'campus_pickup' ? 'when you collect it' : 'when it is delivered';

            return redirect()->route('orders.show', $order)
                ->with('success', "Order placed. Pay GH₵".number_format((float) $order->total_price, 2)." in cash or Mobile Money {$when}.");
        }

        $paymentUrl = app(PaystackPayments::class)->initialize($order, auth()->user()->email);

        if (! $paymentUrl) {
            // Release the stock and keep the cart so the customer can simply try again.
            $order->cancelAndRestoreInventory(auth()->id(), 'Paystack payment could not be started.');

            return redirect()->route('checkout')->withInput()->withErrors([
                'payment' => 'We could not reach Paystack to start your payment. Nothing was charged and your cart is saved. Please try again in a moment.',
            ]);
        }

        // Paystack accepted the order: the cart has become this order.
        auth()->user()->cartItems()->delete();
        session()->forget('referrals');

        return redirect()->away($paymentUrl);
    }

    /** Retry payment for an order the customer left unpaid (closed Paystack, card declined, etc.). */
    public function pay(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if ($order->status !== 'pending') {
            return redirect()->route('orders.show', $order)->with('error', 'This order is no longer waiting for payment.');
        }

        $paymentUrl = app(PaystackPayments::class)->initialize($order, auth()->user()->email);

        if (! $paymentUrl) {
            return redirect()->route('orders.show', $order)->with('error', 'We could not reach Paystack just now. Nothing was charged. Please try again in a moment.');
        }

        return redirect()->away($paymentUrl);
    }

    public function paystackCallback(Request $request, PaystackPayments $paystack)
    {
        $reference = $request->string('reference')->toString() ?: $request->string('trxref')->toString();

        if ($reference === '') {
            return redirect()->route('orders.index')->with('error', 'We could not tell which payment you were returning from. Check your order below.');
        }

        $payment = PaymentTransaction::where('reference', $reference)->firstOrFail();
        $order = $payment->order;
        abort_unless($order->user_id === auth()->id(), 403);

        $data = $paystack->lookup($reference);

        if (($data['status'] ?? null) === 'success') {
            $paystack->complete($reference, $data);

            return redirect()->route('orders.show', $order)->with('success', 'Payment confirmed. Your order is being prepared.');
        }

        if ($data === null) {
            return redirect()->route('orders.show', $order)->with('error', 'We could not confirm your payment with Paystack yet. If you were charged, your order will update automatically within a few minutes.');
        }

        return redirect()->route('orders.show', $order)->with('error', 'Your payment was not completed, so nothing was charged. Use "Pay now" to try again, or cancel the order.');
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
