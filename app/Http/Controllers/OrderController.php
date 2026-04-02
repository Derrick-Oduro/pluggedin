<?php

namespace App\Http\Controllers;

use App\Models\DiscountCampaign;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PointsTransaction;
use App\Models\ProductReview;
use App\Models\ReferralLink;
use App\Notifications\PointsEarnedNotification;
use App\Notifications\RedeemableThresholdReachedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $order->load('items.product');
        $userReviews = ProductReview::query()
            ->where('user_id', auth()->id())
            ->whereIn('product_id', $order->items->pluck('product_id'))
            ->get()
            ->keyBy('product_id');

        $canReviewOrder = in_array($order->status, ['confirmed', 'completed'], true);

        return view('orders.show', compact('order', 'userReviews', 'canReviewOrder'));
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
            'delivery_address' => 'required|string',
            'delivery_phone' => 'required|string',
            'promo_code' => 'nullable|string|max:100',
        ]);

        $cartItems = auth()->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $promoCode = $request->filled('promo_code') ? strtoupper(trim((string) $request->input('promo_code'))) : null;

        DB::transaction(function() use ($request, $cartItems, $promoCode) {
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
                'delivery_address' => $request->delivery_address,
                'delivery_phone' => $request->delivery_phone
            ]);

            if ($campaign) {
                $campaign->increment('used_count');
            }

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price
                ]);

                // Decrease stock
                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);

                $referrals = session('referrals', []);
                $referralLinkId = $referrals[$cartItem->product_id] ?? null;

                if (! $referralLinkId) {
                    continue;
                }

                $referralLink = ReferralLink::where('id', $referralLinkId)
                    ->where('product_id', $cartItem->product_id)
                    ->first();

                if (! $referralLink || $referralLink->user_id === auth()->id()) {
                    continue;
                }

                $points = (int) config('marketplace.referral_points_per_purchase', 10);
                $threshold = (int) config('marketplace.redeemable_points_threshold', 100);

                $referrer = $referralLink->user;
                $previousBalance = (int) $referrer->points_balance;
                $newBalance = $previousBalance + $points;

                $referrer->update(['points_balance' => $newBalance]);

                PointsTransaction::create([
                    'user_id' => $referrer->id,
                    'points' => $points,
                    'type' => 'referral_purchase',
                    'description' => 'Referral conversion from a product purchase.',
                    'meta' => [
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'referral_link_id' => $referralLink->id,
                        'buyer_user_id' => auth()->id(),
                    ],
                ]);

                $referralLink->increment('conversions');

                $referrer->notify(new PointsEarnedNotification($points, 'A purchase was completed via your referral link.'));

                if ($previousBalance < $threshold && $newBalance >= $threshold) {
                    $referrer->notify(new RedeemableThresholdReachedNotification($threshold, $newBalance));
                }
            }

            // Clear cart
            auth()->user()->cartItems()->delete();

            // Clear tracked referrals once checkout is complete.
            session()->forget('referrals');
        });

        return redirect()->route('orders.index')->with('success', 'Order placed successfully!');
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
