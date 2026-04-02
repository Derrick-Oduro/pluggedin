<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use App\Support\AuditLogger;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $reviewedProductIds = $user->reviews()->pluck('product_id');

        $pendingReviews = OrderItem::query()
            ->with(['order', 'product'])
            ->whereHas('order', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->whereIn('status', ['confirmed', 'completed']);
            })
            ->get()
            ->filter(function (OrderItem $item) use ($reviewedProductIds) {
                return $item->product && ! $reviewedProductIds->contains($item->product_id);
            })
            ->unique('product_id')
            ->values();

        $submittedReviews = $user->reviews()->with('product')->latest()->paginate(12);

        return view('reviews.index', [
            'pendingReviews' => $pendingReviews,
            'submittedReviews' => $submittedReviews,
        ]);
    }

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
                'moderation_status' => 'pending',
                'moderation_note' => null,
                'moderated_by' => null,
                'moderated_at' => null,
            ]
        );

        return redirect()->back()->with('success', 'Review saved successfully.');
    }

    public function report(Request $request, ProductReview $review)
    {
        $data = $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        if ((int) $review->user_id === (int) auth()->id()) {
            return redirect()->back()->with('error', 'You cannot report your own review.');
        }

        $review->update([
            'is_reported' => true,
            'report_reason' => $data['reason'] ?? 'Reported by user.',
        ]);

        AuditLogger::log('user.review.reported', ProductReview::class, $review->id, [
            'product_id' => $review->product_id,
            'report_reason' => $review->report_reason,
        ], $request);

        return redirect()->back()->with('success', 'Review reported for admin moderation.');
    }
}
