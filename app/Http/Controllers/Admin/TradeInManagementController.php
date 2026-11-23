<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TradeInRequest;
use App\Notifications\AccountUpdateNotification;
use App\Support\AuditLogger;
use Illuminate\Http\Request;

/**
 * Trade-in workflow: submitted → under review → quoted → (customer) accepted/declined → completed.
 */
class TradeInManagementController extends Controller
{
    private const TABS = [
        'to_quote' => ['submitted', 'under_review'],
        'quoted' => ['quoted'],
        'accepted' => ['accepted'],
        'closed' => ['completed', 'declined'],
    ];

    public function index(Request $request)
    {
        $counts = TradeInRequest::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $tabCounts = collect(self::TABS)->map(fn ($statuses) => (int) $counts->only($statuses)->sum())->put('all', (int) $counts->sum());

        // Open on the first tab that has work in it.
        $tab = $request->query('tab');
        if (! $tab || ! $tabCounts->has($tab)) {
            $tab = collect(['to_quote', 'accepted'])->first(fn ($t) => $tabCounts[$t] > 0) ?? 'all';
        }

        $requests = TradeInRequest::with(['user', 'desiredProduct'])
            ->when($tab !== 'all', fn ($query) => $query->whereIn('status', self::TABS[$tab]))
            ->orderByRaw("case when status in ('submitted','under_review','accepted') then 0 else 1 end")
            ->oldest()
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.trade-ins.index', compact('requests', 'tab', 'tabCounts'));
    }

    public function review(Request $request, TradeInRequest $tradeIn)
    {
        abort_unless($tradeIn->status === 'submitted', 422, 'This trade-in is already being handled.');
        $tradeIn->update(['status' => 'under_review']);
        AuditLogger::log('superadmin.trade_in.reviewing', TradeInRequest::class, $tradeIn->id, ['device' => "{$tradeIn->brand} {$tradeIn->model}"], $request);

        return back()->with('success', "Marked {$tradeIn->brand} {$tradeIn->model} as under review.");
    }

    public function quote(Request $request, TradeInRequest $tradeIn)
    {
        abort_unless(in_array($tradeIn->status, ['submitted', 'under_review', 'quoted'], true), 422, 'This trade-in can no longer be quoted.');
        $data = $request->validate(['quoted_value' => 'required|numeric|min:1|max:1000000', 'admin_notes' => 'nullable|string|max:2000']);

        $requoted = $tradeIn->status === 'quoted';
        $tradeIn->update([...$data, 'status' => 'quoted', 'quoted_at' => now()]);

        $value = 'GH₵'.number_format((float) $data['quoted_value'], 2);
        $tradeIn->user?->notify(new AccountUpdateNotification(
            'trade_in',
            $requoted ? 'Your trade-in quote was updated' : 'Your trade-in has a quote',
            "PluggedIn offers {$value} for your {$tradeIn->brand} {$tradeIn->model}. Accept or decline it in My trade-ins.",
            route('trade-ins.index'),
            'View quote',
        ));
        AuditLogger::log('superadmin.trade_in.quoted', TradeInRequest::class, $tradeIn->id, ['device' => "{$tradeIn->brand} {$tradeIn->model}", 'value' => $value], $request);

        return back()->with('success', "Quote of {$value} sent to {$tradeIn->user?->name}.");
    }

    public function complete(Request $request, TradeInRequest $tradeIn)
    {
        abort_unless($tradeIn->status === 'accepted', 422, 'Only accepted trade-ins can be completed.');
        $data = $request->validate(['admin_notes' => 'nullable|string|max:2000']);

        $tradeIn->update(['status' => 'completed', 'admin_notes' => ($data['admin_notes'] ?? null) ?: $tradeIn->admin_notes]);
        $tradeIn->user?->notify(new AccountUpdateNotification(
            'trade_in',
            'Trade-in complete',
            "Your {$tradeIn->brand} {$tradeIn->model} trade-in is complete. Thanks for trading with PluggedIn!",
            route('trade-ins.index'),
            'My trade-ins',
        ));
        AuditLogger::log('superadmin.trade_in.completed', TradeInRequest::class, $tradeIn->id, ['device' => "{$tradeIn->brand} {$tradeIn->model}"], $request);

        return back()->with('success', "{$tradeIn->brand} {$tradeIn->model} trade-in marked as completed.");
    }
}
