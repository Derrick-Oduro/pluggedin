<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCampaign;
use App\Models\HeroSlide;
use App\Support\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * "Homepage & discounts": the homepage hero image and discount campaigns.
 * Every action returns to the section it came from (kept in the session, so redirects stay simple).
 */
class SuperAdminMarketingController extends Controller
{
    private const CAMPAIGN_TABS = ['live', 'scheduled', 'paused', 'finished', 'archived'];

    public function index(Request $request)
    {
        // After a failed form, reopen the section (and form) it came from.
        $section = $request->query('section', session('section', old('section', 'discounts')));
        $section = in_array($section, ['discounts', 'homepage'], true) ? $section : 'discounts';
        $editingKey = (string) (session('editing') ?? old('form_key', ''));

        $slides = HeroSlide::query()->orderByDesc('is_active')->orderBy('sort_order')->orderBy('id')->get();
        $archivedSlides = HeroSlide::onlyTrashed()->latest('deleted_at')->get();

        $campaigns = DiscountCampaign::query()->withCount('orders')->latest()->get();
        $archivedCampaigns = DiscountCampaign::onlyTrashed()->latest('deleted_at')->get();

        $grouped = $campaigns->groupBy(fn (DiscountCampaign $c) => in_array($c->state(), ['ended', 'used_up'], true) ? 'finished' : $c->state());
        $tabCounts = collect(self::CAMPAIGN_TABS)->mapWithKeys(fn ($tab) => [$tab => $tab === 'archived' ? $archivedCampaigns->count() : ($grouped[$tab] ?? collect())->count()])->all();

        $tab = $request->query('tab', session('tab'));
        if (! in_array($tab, self::CAMPAIGN_TABS, true)) {
            $tab = collect(['live', 'scheduled', 'paused'])->first(fn ($t) => $tabCounts[$t] > 0) ?? 'live';
        }
        $shown = $tab === 'archived' ? $archivedCampaigns : ($grouped[$tab] ?? collect());

        // Only the biggest automatic (no-code) discount is applied at checkout and shown on the homepage.
        $liveAutomatic = ($grouped['live'] ?? collect())->filter->isAutomatic()->sortByDesc('discount_percent')->values();
        $homepageCampaign = DiscountCampaign::active()->orderByDesc('discount_percent')->orderByDesc('starts_at')->first();

        return view('superadmin.marketing.index', compact(
            'section', 'editingKey', 'slides', 'archivedSlides', 'campaigns', 'archivedCampaigns', 'tab', 'tabCounts', 'shown', 'liveAutomatic', 'homepageCampaign'
        ));
    }

    // ---------------------------------------------------------------- Homepage image

    public function storeSlide(Request $request)
    {
        $data = $this->validateSlide($request);
        $makeLive = $request->boolean('make_live') || $request->boolean('is_active'); // is_active: older form
        $data['is_active'] = false;
        $data['sort_order'] = 999;

        $slide = HeroSlide::create($data);
        AuditLogger::log('superadmin.slide.created', HeroSlide::class, $slide->id, ['title' => $slide->title], $request);

        if ($makeLive) {
            $this->setLiveSlide($slide);

            return $this->back('homepage', 'Image added and now on the homepage.');
        }

        return $this->back('homepage', 'Image added to the library.');
    }

    public function updateSlide(Request $request, HeroSlide $slide)
    {
        $data = $this->validateSlide($request, $slide);
        $slide->update($data);
        AuditLogger::log('superadmin.slide.updated', HeroSlide::class, $slide->id, ['title' => $slide->title], $request);

        return $this->back('homepage', 'Image details saved.'.($slide->is_active ? ' The homepage shows the change now.' : ''));
    }

    public function destroySlide(HeroSlide $slide)
    {
        $wasLive = $slide->is_active;
        $slide->update(['is_active' => false]);
        $slide->delete();
        AuditLogger::log('superadmin.slide.deleted', HeroSlide::class, $slide->id, ['title' => $slide->title], request());

        return $this->back('homepage', $wasLive
            ? 'Image removed. The homepage now shows the default design until you pick another image.'
            : 'Image moved to Removed images. You can restore it from there.');
    }

    public function makeSlideLive(HeroSlide $slide)
    {
        $this->setLiveSlide($slide);

        return $this->back('homepage', '“'.($slide->title ?: 'Untitled image').'” is now on the homepage.');
    }

    /** Older picker form; kept so existing links keep working. */
    public function selectLiveSlide(Request $request)
    {
        $data = $request->validate(['slide_id' => ['required', 'integer', 'exists:hero_slides,id']]);

        return $this->makeSlideLive(HeroSlide::findOrFail($data['slide_id']));
    }

    public function takeSlideOffHomepage(HeroSlide $slide)
    {
        $slide->update(['is_active' => false]);
        AuditLogger::log('superadmin.slide.updated', HeroSlide::class, $slide->id, ['title' => $slide->title, 'is_active' => false], request());

        return $this->back('homepage', 'Image taken off the homepage. Visitors now see the default design.');
    }

    public function restoreSlide(int $id)
    {
        $slide = HeroSlide::onlyTrashed()->findOrFail($id);
        $slide->restore();
        AuditLogger::log('superadmin.slide.restored', HeroSlide::class, $slide->id, ['title' => $slide->title], request());

        return $this->back('homepage', 'Image restored to the library.');
    }

    private function setLiveSlide(HeroSlide $slide): void
    {
        DB::transaction(function () use ($slide) {
            HeroSlide::query()->whereKeyNot($slide->id)->update(['is_active' => false]);
            $slide->update(['is_active' => true, 'sort_order' => 0]);
        });
        AuditLogger::log('superadmin.hero_image.made_live', HeroSlide::class, $slide->id, ['title' => $slide->title], request());
    }

    private function validateSlide(Request $request, ?HeroSlide $slide = null): array
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:120',
            'image_url' => [$slide ? 'nullable' : 'required_without:image_file', 'nullable', 'url', 'max:2000'],
            'image_file' => 'nullable|image|max:5120',
            'alt_text' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image_file')) {
            $path = \App\Support\ImageOptimizer::store($request->file('image_file'), 'hero-slides', 2400, 85);
            $data['image_url'] = Storage::disk('public')->url($path);
        } elseif ($slide && empty($data['image_url'])) {
            $data['image_url'] = $slide->image_url; // keep the current image
        }
        unset($data['image_file']);

        return $data;
    }

    // ---------------------------------------------------------------- Discounts

    public function storeCampaign(Request $request)
    {
        $data = $this->validateCampaign($request);
        if ($data instanceof \Illuminate\Http\RedirectResponse) {
            return $data;
        }

        $campaign = DiscountCampaign::create($data);
        AuditLogger::log('superadmin.campaign.created', DiscountCampaign::class, $campaign->id, [
            'name' => $campaign->name, 'code' => $campaign->code, 'discount_percent' => $campaign->discount_percent, 'max_uses' => $campaign->max_uses,
        ], $request);

        $state = $campaign->state();

        return $this->back('discounts', "“{$campaign->name}” created and ".strtolower(DiscountCampaign::STATES[$state][0]).'.', $state === 'used_up' || $state === 'ended' ? 'finished' : $state);
    }

    public function updateCampaign(Request $request, DiscountCampaign $campaign)
    {
        $data = $this->validateCampaign($request, $campaign);
        if ($data instanceof \Illuminate\Http\RedirectResponse) {
            return $data;
        }

        $campaign->update($data);
        AuditLogger::log('superadmin.campaign.updated', DiscountCampaign::class, $campaign->id, [
            'name' => $campaign->name, 'code' => $campaign->code, 'discount_percent' => $campaign->discount_percent,
            'max_uses' => $campaign->max_uses, 'is_active' => $campaign->is_active,
        ], $request);

        $state = $campaign->state();

        return $this->back('discounts', "“{$campaign->name}” saved. It is now ".strtolower(DiscountCampaign::STATES[$state][0]).'.', in_array($state, ['ended', 'used_up'], true) ? 'finished' : $state);
    }

    /** One-click state changes: pause, resume, end now. */
    public function changeCampaignState(Request $request, DiscountCampaign $campaign)
    {
        $action = $request->validate(['action' => 'required|in:pause,resume,end'])['action'];

        if ($action === 'resume' && in_array($campaign->state(), ['ended', 'used_up'], true)) {
            return $this->back('discounts', 'This campaign has finished. Edit it to give it a new end date or a higher usage limit first.', 'finished', 'error');
        }

        $campaign->update(match ($action) {
            'pause' => ['is_active' => false],
            'resume' => ['is_active' => true],
            'end' => ['ends_at' => now(), 'is_active' => false],
        });
        AuditLogger::log('superadmin.campaign.'.['pause' => 'paused', 'resume' => 'resumed', 'end' => 'ended'][$action], DiscountCampaign::class, $campaign->id, ['name' => $campaign->name], $request);

        $state = $campaign->state();
        $message = match ($action) {
            'pause' => "“{$campaign->name}” is paused. Shoppers can't use it until you resume it.",
            'resume' => $state === 'scheduled'
                ? "“{$campaign->name}” is back on and will start {$campaign->starts_at->format('M j \a\t H:i')}."
                : "“{$campaign->name}” is live again.",
            'end' => "“{$campaign->name}” has ended.",
        };

        return $this->back('discounts', $message, in_array($state, ['ended', 'used_up'], true) ? 'finished' : $state);
    }

    public function duplicateCampaign(Request $request, DiscountCampaign $campaign)
    {
        $copy = $campaign->replicate(['used_count']);
        $copy->fill([
            'name' => $campaign->name.' (copy)',
            'code' => $campaign->code ? $this->uniqueCode($campaign->code) : null,
            'is_active' => false,
            'used_count' => 0,
            'starts_at' => null,
            'ends_at' => null,
        ])->save();
        AuditLogger::log('superadmin.campaign.created', DiscountCampaign::class, $copy->id, ['name' => $copy->name, 'copied_from' => $campaign->id], $request);

        return $this->back('discounts', "Copied as “{$copy->name}”. It's paused so you can set dates first.", 'paused');
    }

    public function destroyCampaign(DiscountCampaign $campaign)
    {
        $campaign->update(['is_active' => false]);
        $campaign->delete();
        AuditLogger::log('superadmin.campaign.deleted', DiscountCampaign::class, $campaign->id, ['name' => $campaign->name, 'code' => $campaign->code], request());

        return $this->back('discounts', "“{$campaign->name}” archived. Restore it from the Archived tab.", 'archived');
    }

    public function restoreCampaign(int $id)
    {
        $campaign = DiscountCampaign::onlyTrashed()->findOrFail($id);
        $campaign->restore();
        // Restored paused, and with a fresh code if another campaign took it meanwhile.
        $codeTaken = $campaign->code && DiscountCampaign::where('code', $campaign->code)->whereKeyNot($campaign->id)->exists();
        $campaign->update(['is_active' => false] + ($codeTaken ? ['code' => $this->uniqueCode($campaign->code)] : []));
        AuditLogger::log('superadmin.campaign.restored', DiscountCampaign::class, $campaign->id, ['name' => $campaign->name, 'code' => $campaign->code], request());

        return $this->back('discounts', "“{$campaign->name}” restored. It's paused until you resume it.", 'paused');
    }

    /**
     * Shared create/edit validation. Every field is always submitted by the form,
     * so saving never wipes dates or limits that weren't shown.
     */
    private function validateCampaign(Request $request, ?DiscountCampaign $campaign = null): array|\Illuminate\Http\RedirectResponse
    {
        $request->merge(['code' => $request->filled('code') ? strtoupper(trim((string) $request->input('code'))) : null]);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'nullable', 'string', 'max:40', 'regex:/^[A-Z0-9_-]+$/',
                Rule::unique('discount_campaigns', 'code')->ignore($campaign?->id)->whereNull('deleted_at'),
            ],
            'discount_percent' => 'required|integer|min:1|max:95',
            'max_uses' => 'nullable|integer|min:1|max:1000000',
            'description' => 'nullable|string|max:2000',
            'starts_date' => 'nullable|date',
            'starts_time' => 'nullable|date_format:H:i',
            'ends_date' => 'nullable|date',
            'ends_time' => 'nullable|date_format:H:i',
            'is_active' => 'nullable|boolean',
        ], [
            'code.regex' => 'Use only letters, numbers, dashes or underscores, e.g. BACK2SCHOOL.',
            'code.unique' => 'Another campaign already uses this code.',
        ]);

        $fail = fn (string $field, string $message) => redirect()->back()->withErrors([$field => $message])->withInput()
            ->with('section', 'discounts')->with('editing', $campaign?->id ?? 'new');

        if ($campaign && isset($data['max_uses']) && (int) $data['max_uses'] < (int) $campaign->used_count) {
            return $fail('max_uses', "It has already been used {$campaign->used_count} times, so the limit can't be lower than that.");
        }

        $startsAt = $this->combineDateAndTime($data['starts_date'] ?? null, $data['starts_time'] ?? null, '00:00');
        $endsAt = $this->combineDateAndTime($data['ends_date'] ?? null, $data['ends_time'] ?? null, '23:59');
        if ($startsAt && $endsAt && $endsAt->lte($startsAt)) {
            return $fail('ends_date', 'The end must be after the start.');
        }

        unset($data['starts_date'], $data['starts_time'], $data['ends_date'], $data['ends_time']);

        return array_merge($data, [
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : ($campaign?->is_active ?? true),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);
    }

    private function combineDateAndTime(?string $date, ?string $time, string $defaultTime): ?Carbon
    {
        return $date ? Carbon::createFromFormat('Y-m-d H:i', $date.' '.($time ?: $defaultTime)) : null;
    }

    private function uniqueCode(string $code): string
    {
        $base = substr($code, 0, 34);
        for ($i = 2; DiscountCampaign::withTrashed()->where('code', "{$base}-{$i}")->exists(); $i++);

        return "{$base}-{$i}";
    }

    private function back(string $section, string $message, ?string $tab = null, string $type = 'success')
    {
        return redirect()->route('superadmin.marketing.index')
            ->with($type, $message)
            ->with('section', $section)
            ->with('tab', $tab);
    }
}
