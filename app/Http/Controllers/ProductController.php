<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ReferralLink;
use App\Models\WishlistItem;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::approved()->with('category');

        $keyword = trim((string) $request->query('q', ''));
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $availability = (string) $request->query('availability', 'all');
        $sort = (string) $request->query('sort', 'latest');

        $minPrice = is_numeric($minPrice) ? (float) $minPrice : null;
        $maxPrice = is_numeric($maxPrice) ? (float) $maxPrice : null;

        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }

        if (! in_array($availability, ['all', 'in_stock', 'out_of_stock'], true)) {
            $availability = 'all';
        }

        if (! in_array($sort, ['latest', 'price_asc', 'price_desc', 'name_asc'], true)) {
            $sort = 'latest';
        }

        if ($keyword !== '') {
            $query->where(function ($builder) use ($keyword) {
                $builder->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $query->when($minPrice !== null, fn ($builder) => $builder->where('price', '>=', $minPrice));
        $query->when($maxPrice !== null, fn ($builder) => $builder->where('price', '<=', $maxPrice));
        $query->when($availability === 'in_stock', fn ($builder) => $builder->where('stock_quantity', '>', 0));
        $query->when($availability === 'out_of_stock', fn ($builder) => $builder->where('stock_quantity', '<=', 0));

        if ($sort === 'price_asc') {
            $query->orderBy('price');
        } elseif ($sort === 'price_desc') {
            $query->orderByDesc('price');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('name');
        } else {
            $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        if ($product->status !== 'approved') {
            if (! auth()->check() || (! auth()->user()->isAdmin() && auth()->id() !== $product->uploaded_by)) {
                abort(404);
            }
        }

        $product->load('category');

        $relatedProducts = Product::approved()->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        $userReview = null;
        $canReview = false;
        $referralLink = null;
        $isWishlisted = false;
        $wishlistItemId = null;

        if (auth()->check()) {
            $userReview = ProductReview::where('product_id', $product->id)
                ->where('user_id', auth()->id())
                ->first();

            $hasVerifiedPurchase = OrderItem::where('product_id', $product->id)
                ->whereHas('order', function ($query) {
                    $query->where('user_id', auth()->id())
                        ->whereIn('status', ['confirmed', 'completed']);
                })
                ->exists();

            $canReview = $hasVerifiedPurchase && ! $userReview;

            $referralLink = ReferralLink::where('product_id', $product->id)
                ->where('user_id', auth()->id())
                ->first();

            $wishlistItem = WishlistItem::query()
                ->where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->first();

            $isWishlisted = (bool) $wishlistItem;
            $wishlistItemId = $wishlistItem?->id;
        }

        $reviews = $product->reviews()
            ->approved()
            ->with('user')
            ->latest()
            ->get();

        return view('products.show', compact('product', 'relatedProducts', 'canReview', 'userReview', 'reviews', 'referralLink', 'isWishlisted', 'wishlistItemId'));
    }
}
