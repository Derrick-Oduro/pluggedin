<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Support\AuditLogger;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        $reviews = ProductReview::query()
            ->with(['user', 'product', 'order'])
            ->when($status !== 'all', function ($query) use ($status) {
                $query->where('moderation_status', $status);
            })
            ->when($request->boolean('reported_only'), function ($query) {
                $query->where('is_reported', true);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'pending' => ProductReview::where('moderation_status', 'pending')->count(),
            'approved' => ProductReview::where('moderation_status', 'approved')->count(),
            'hidden' => ProductReview::where('moderation_status', 'hidden')->count(),
            'reported' => ProductReview::where('is_reported', true)->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats', 'status'));
    }

    public function moderate(Request $request, ProductReview $review)
    {
        $data = $request->validate([
            'moderation_status' => 'required|in:approved,hidden',
            'moderation_note' => 'nullable|string|max:1000',
        ]);

        $review->update([
            'moderation_status' => $data['moderation_status'],
            'moderation_note' => $data['moderation_note'] ?? null,
            'moderated_by' => auth()->id(),
            'moderated_at' => now(),
        ]);

        AuditLogger::log('admin.review.moderated', ProductReview::class, $review->id, [
            'moderation_status' => $review->moderation_status,
            'is_reported' => $review->is_reported,
        ], $request);

        return redirect()->back()->with('success', 'Review moderation status updated.');
    }
}
