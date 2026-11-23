<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductReport;
use App\Notifications\ProductModerationUpdatedNotification;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductReportManagementController extends Controller
{
    public const REASONS = [
        'suspicious_listing' => 'Looks like a scam',
        'incorrect_details' => 'Wrong details',
        'prohibited_item' => 'Not allowed on PluggedIn',
        'other' => 'Something else',
    ];

    public function index(Request $request)
    {
        $counts = ProductReport::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $tabCounts = [
            'open' => (int) ($counts['open'] ?? 0),
            'resolved' => (int) ($counts['resolved'] ?? 0),
            'dismissed' => (int) ($counts['dismissed'] ?? 0),
            'all' => (int) $counts->sum(),
        ];
        $status = array_key_exists($request->query('status'), $tabCounts) ? $request->query('status') : 'open';

        $reports = ProductReport::with(['product.uploader', 'product.category', 'reporter'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->when($status === 'open', fn ($q) => $q->oldest(), fn ($q) => $q->latest('updated_at'))
            ->paginate(20)
            ->withQueryString();

        // How many open reports each listing on this page has, so repeat offenders stand out.
        $openPerProduct = ProductReport::where('status', 'open')
            ->whereIn('product_id', $reports->pluck('product_id'))
            ->selectRaw('product_id, count(*) as total')->groupBy('product_id')->pluck('total', 'product_id');

        return view('superadmin.product-reports.index', [
            'reports' => $reports,
            'status' => $status,
            'tabCounts' => $tabCounts,
            'openPerProduct' => $openPerProduct,
            'reasons' => self::REASONS,
        ]);
    }

    /**
     * resolve  - dealt with (e.g. seller fixed the listing), listing stays up
     * dismiss  - nothing wrong, listing stays up
     * takedown - hide the listing, tell the seller why, and close every open report on it
     */
    public function update(Request $request, ProductReport $report)
    {
        $data = $request->validate([
            'action' => 'required_without:status|in:resolve,dismiss,takedown',
            'status' => 'nullable|in:resolved,dismissed', // older forms
            'note' => 'nullable|string|max:500',
        ]);
        $action = $data['action'] ?? ($data['status'] === 'dismissed' ? 'dismiss' : 'resolve');
        $product = $report->product;

        if ($action === 'takedown') {
            abort_unless($product, 422, 'This listing no longer exists.');
            $note = ($data['note'] ?? null) ?: 'Removed after a report: '.(self::REASONS[$report->reason] ?? $report->reason).'.';

            $closed = DB::transaction(function () use ($product, $note, $request) {
                $product->update([
                    'status' => 'rejected',
                    'admin_review_comment' => $note,
                    'reviewed_by' => $request->user()->id,
                    'reviewed_at' => now(),
                ]);

                return ProductReport::where('product_id', $product->id)->where('status', 'open')->update(['status' => 'resolved']);
            });

            if ($product->uploader) {
                $product->uploader->notify(new ProductModerationUpdatedNotification($product->id, $product->name, 'removed', $note));
            }
            AuditLogger::log('superadmin.listing.removed', Product::class, $product->id, ['name' => $product->name, 'reason' => $note, 'reports_closed' => $closed], $request);

            return back()->with('success', "“{$product->name}” was taken down and {$closed} ".\Illuminate\Support\Str::plural('report', $closed).' closed. The seller has been told why.');
        }

        $report->update(['status' => $action === 'dismiss' ? 'dismissed' : 'resolved']);
        AuditLogger::log('superadmin.report.'.($action === 'dismiss' ? 'dismissed' : 'resolved'), ProductReport::class, $report->id, ['product' => $product?->name, 'reason' => $report->reason], $request);

        return back()->with('success', $action === 'dismiss' ? 'Report dismissed. The listing stays up.' : 'Report marked as resolved.');
    }
}
