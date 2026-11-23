<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductReport;
use App\Models\User;
use App\Notifications\AccountUpdateNotification;
use App\Support\AuditLogger;
use Illuminate\Http\Request;

class SellerVerificationController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $base = User::whereHas('uploadedProducts');
        $tabCounts = [
            'unverified' => (clone $base)->whereNull('seller_verified_at')->count(),
            'verified' => (clone $base)->whereNotNull('seller_verified_at')->count(),
            'all' => (clone $base)->count(),
        ];
        $tab = array_key_exists($request->query('tab'), $tabCounts) ? $request->query('tab') : 'all';

        $sellers = $base
            ->withCount([
                'uploadedProducts',
                'uploadedProducts as live_products_count' => fn ($q) => $q->where('status', 'approved')->where('listing_state', 'active'),
                'uploadedProducts as rejected_products_count' => fn ($q) => $q->where('status', 'rejected'),
            ])
            ->when($tab === 'verified', fn ($q) => $q->whereNotNull('seller_verified_at'))
            ->when($tab === 'unverified', fn ($q) => $q->whereNull('seller_verified_at'))
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))
            ->orderByDesc('live_products_count')
            ->paginate(20)
            ->withQueryString();

        $openReports = ProductReport::where('product_reports.status', 'open')
            ->join('products', 'products.id', '=', 'product_reports.product_id')
            ->whereIn('products.uploaded_by', $sellers->pluck('id'))
            ->selectRaw('products.uploaded_by as seller_id, count(*) as total')
            ->groupBy('products.uploaded_by')
            ->pluck('total', 'seller_id');

        return view('superadmin.sellers.index', compact('sellers', 'tab', 'tabCounts', 'search', 'openReports'));
    }

    public function update(Request $request, User $seller)
    {
        abort_unless($seller->uploadedProducts()->exists(), 422);

        $verify = $seller->seller_verified_at === null;
        // Not mass-assignable on purpose, so members can never set it themselves.
        $seller->forceFill(['seller_verified_at' => $verify ? now() : null])->save();

        if ($verify) {
            $seller->notify(new AccountUpdateNotification(
                'seller_verified',
                "You're a verified seller",
                'PluggedIn has verified you. Buyers now see a verified badge on your listings.',
                route('products.upload.index'),
                'My listings',
            ));
        }
        AuditLogger::log('superadmin.seller.'.($verify ? 'verified' : 'unverified'), User::class, $seller->id, ['name' => $seller->name, 'email' => $seller->email], $request);

        return back()->with('success', $verify ? "{$seller->name} is now verified and has been notified." : "Verification removed from {$seller->name}.");
    }
}
