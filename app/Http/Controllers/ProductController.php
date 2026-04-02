<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ReferralLink;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::approved()->with('category');

        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $products = $query->paginate(12);
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
        }

        $reviews = $product->reviews()
            ->approved()
            ->with('user')
            ->latest()
            ->get();

        return view('products.show', compact('product', 'relatedProducts', 'canReview', 'userReview', 'reviews', 'referralLink'));
    }
}
