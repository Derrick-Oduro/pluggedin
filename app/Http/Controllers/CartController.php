<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = auth()->user()->cartItems()->with('product.category')->get();
        $total = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'nullable|integer|min:1'
        ]);

        if ($product->status !== 'approved' || $product->listing_state !== 'active') {
            return redirect()->back()->with('error', 'This item is not available to buy right now.');
        }

        if ($product->stock_quantity < 1) {
            return redirect()->back()->with('error', "{$product->name} is sold out.");
        }

        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        $wanted = ($cartItem?->quantity ?? 0) + (int) $request->input('quantity', 1);
        $quantity = min($wanted, $product->stock_quantity);

        CartItem::updateOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $product->id],
            ['quantity' => $quantity]
        );

        if ($quantity < $wanted) {
            return redirect()->back()->with('success', "Added to cart. Only {$product->stock_quantity} available, so your cart has {$quantity}.");
        }

        return redirect()->back()->with('success', 'Added to cart.');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $available = (int) $cartItem->product->stock_quantity;
        $quantity = min((int) $request->quantity, max($available, 1));
        $cartItem->update(['quantity' => $quantity]);

        if ($quantity < (int) $request->quantity) {
            return redirect()->back()->with('success', "Only {$available} available, so your cart has {$quantity}.");
        }

        return redirect()->back()->with('success', 'Cart updated.');
    }

    public function remove(CartItem $cartItem)
    {
        if ($cartItem->user_id !== auth()->id()) {
            abort(403);
        }

        $cartItem->delete();

        return redirect()->back()->with('success', 'Item removed from cart.');
    }
}
