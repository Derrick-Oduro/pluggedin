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

        if (! self::buyable($product)) {
            return redirect()->back()->with('error', $product && $product->stock_quantity < 1
                ? "{$product->name} is sold out. We'll keep it in your saved items."
                : 'This product is no longer available.');
        }

        $this->addOneToCart($product);
        $wishlistItem->delete();

        return redirect()->back()->with('success', "{$product->name} moved to your cart.");
    }

    /** Move every saved item that can be bought right now into the cart. */
    public function moveAllToCart()
    {
        $items = auth()->user()->wishlistItems()->with('product')->get()
            ->filter(fn (WishlistItem $item) => self::buyable($item->product));

        if ($items->isEmpty()) {
            return redirect()->back()->with('error', 'None of your saved items are available to buy right now.');
        }

        foreach ($items as $item) {
            $this->addOneToCart($item->product);
            $item->delete();
        }

        return redirect()->route('cart.index')->with('success', $items->count().' saved '.\Illuminate\Support\Str::plural('item', $items->count()).' moved to your cart.');
    }

    private static function buyable(?Product $product): bool
    {
        return $product
            && $product->status === 'approved'
            && $product->listing_state === 'active'
            && $product->stock_quantity > 0;
    }

    /** Add one to the cart without going over what's in stock. */
    private function addOneToCart(Product $product): void
    {
        $cartItem = CartItem::firstOrNew(['user_id' => auth()->id(), 'product_id' => $product->id]);
        $cartItem->quantity = min(($cartItem->quantity ?? 0) + 1, $product->stock_quantity);
        $cartItem->save();
    }
}
