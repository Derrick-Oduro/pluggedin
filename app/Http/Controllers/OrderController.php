<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PointsTransaction;
use App\Models\ReferralLink;
use App\Notifications\PointsEarnedNotification;
use App\Notifications\RedeemableThresholdReachedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        return view('orders.show', compact('order'));
    }

    public function checkout()
    {
        $cartItems = auth()->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        $total = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        return view('orders.checkout', compact('cartItems', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'delivery_address' => 'required|string',
            'delivery_phone' => 'required|string'
        ]);

        $cartItems = auth()->user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        DB::transaction(function() use ($request, $cartItems) {
            $total = $cartItems->sum(function($item) {
                return $item->product->price * $item->quantity;
            });

            $order = Order::create([
                'user_id' => auth()->id(),
                'total_price' => $total,
                'status' => 'pending',
                'delivery_address' => $request->delivery_address,
                'delivery_phone' => $request->delivery_phone
            ]);

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
}
