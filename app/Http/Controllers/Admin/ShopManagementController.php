<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Notifications\AccountUpdateNotification;
use App\Support\AuditLogger;
use Illuminate\Http\Request;

class ShopManagementController extends Controller
{
    public const PLANS = ['starter' => 'Starter', 'campus_pro' => 'Campus Pro', 'business' => 'Business'];

    public function index(Request $request)
    {
        $counts = Shop::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $tabCounts = [
            'pending' => (int) ($counts['pending'] ?? 0),
            'approved' => (int) ($counts['approved'] ?? 0),
            'suspended' => (int) ($counts['suspended'] ?? 0),
            'all' => (int) $counts->sum(),
        ];

        $status = array_key_exists($request->query('status'), $tabCounts)
            ? $request->query('status')
            : ($tabCounts['pending'] > 0 ? 'pending' : 'all');

        $shops = Shop::with('owner')
            ->withCount(['products', 'products as live_products_count' => fn ($q) => $q->where('status', 'approved')])
            ->when($status !== 'all', fn ($query) => $query->where('status', $status))
            ->when($status === 'pending', fn ($query) => $query->oldest(), fn ($query) => $query->latest())
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.shops.index', ['shops' => $shops, 'status' => $status, 'tabCounts' => $tabCounts, 'plans' => self::PLANS]);
    }

    public function update(Request $request, Shop $shop)
    {
        $data = $request->validate([
            'status' => 'required|in:approved,suspended,pending',
            'plan' => 'required|in:'.implode(',', array_keys(self::PLANS)),
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $previous = $shop->status;
        $shop->update($data);

        if ($previous !== $shop->status && $shop->owner) {
            [$title, $message, $url, $label] = match ($shop->status) {
                'approved' => ['Your shop is approved', "{$shop->name} is now live on PluggedIn. You can start adding products.", route('shops.dashboard'), 'Go to my shop'],
                'suspended' => ['Your shop has been suspended', "{$shop->name} is hidden from the marketplace for now.".(! empty($data['admin_notes']) ? " Reason: {$data['admin_notes']}" : ' Contact us for details.'), route('contact'), 'Contact us'],
                default => ['Your shop is back under review', "{$shop->name} is being reviewed again by PluggedIn.", route('shops.dashboard'), 'My shop'],
            };
            $shop->owner->notify(new AccountUpdateNotification('shop', $title, $message, $url, $label));
        }

        $action = $previous !== $shop->status ? ($shop->status === 'approved' ? 'approved' : ($shop->status === 'suspended' ? 'suspended' : 'updated')) : 'updated';
        AuditLogger::log("superadmin.shop.{$action}", Shop::class, $shop->id, ['name' => $shop->name, 'plan' => $shop->plan, 'status' => $shop->status], $request);

        return back()->with('success', match (true) {
            $previous !== 'approved' && $shop->status === 'approved' => "{$shop->name} approved. The owner has been notified.",
            $previous !== 'suspended' && $shop->status === 'suspended' => "{$shop->name} suspended. The owner has been notified.",
            default => "{$shop->name} updated.",
        });
    }
}
