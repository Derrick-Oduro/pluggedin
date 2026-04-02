<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1500',
        ]);

        $verifiedOrderItem = OrderItem::query()
            ->where('product_id', $product->id)
            ->whereHas('order', function ($query) {
                $query->where('user_id', auth()->id())
                    ->whereIn('status', ['confirmed', 'completed']);
            })
            ->latest()
            ->first();

        if (! $verifiedOrderItem) {
            return redirect()
                ->back()
                ->with('error', 'Only verified buyers can leave reviews.');
        }

        ProductReview::updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id' => auth()->id(),
            ],
            [
                'order_id' => $verifiedOrderItem->order_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );

        return redirect()->back()->with('success', 'Review saved successfully.');
    }
}
