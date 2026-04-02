<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\WishlistItem;

class WishlistController extends Controller
{
    public function index()
    {
        $items = auth()->user()
            ->wishlistItems()
            ->with('product.category')
            ->latest()
            ->paginate(12);

        return view('wishlist.index', compact('items'));
    }

    public function store(Product $product)
    {
        if ($product->status !== 'approved') {
            abort(404);
        }

        WishlistItem::firstOrCreate([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
        ]);

        return redirect()->back()->with('success', 'Saved to your wishlist.');
    }

    public function destroy(WishlistItem $wishlistItem)
    {
        if ($wishlistItem->user_id !== auth()->id()) {
            abort(403);
        }

        $wishlistItem->delete();

        return redirect()->back()->with('success', 'Item removed from wishlist.');
    }

    public function moveToCart(WishlistItem $wishlistItem)
    {
        if ($wishlistItem->user_id !== auth()->id()) {
            abort(403);
        }

        $product = $wishlistItem->product;

        if (! $product || $product->status !== 'approved') {
            return redirect()->back()->with('error', 'This product is no longer available.');
        }

        $cartItem = CartItem::query()
            ->where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity');
        } else {
            CartItem::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => 1,
            ]);
        }

        $wishlistItem->delete();

        return redirect()->route('cart.index')->with('success', 'Item moved to cart.');
    }
}
