<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DiscountCampaign;
use App\Models\HeroSlide;
use App\Support\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SuperAdminMarketingController extends Controller
{
    public function index()
    {
        $slides = HeroSlide::query()->orderBy('sort_order')->orderBy('id')->get();
        $campaigns = DiscountCampaign::query()->latest()->get();

        return view('superadmin.marketing.index', compact('slides', 'campaigns'));
    }

    public function storeSlide(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:1000',
            'image_url' => 'required|url|max:2000',
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $slide = HeroSlide::create($data);
        AuditLogger::log('superadmin.slide.created', HeroSlide::class, $slide?->id, [
            'title' => $slide?->title,
            'sort_order' => $slide?->sort_order,
        ], $request);

        return redirect()->route('superadmin.marketing.index')->with('success', 'Carousel slide created.');
    }

    public function updateSlide(Request $request, HeroSlide $slide)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:1000',
            'image_url' => 'required|url|max:2000',
            'alt_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        $slide->update($data);

        AuditLogger::log('superadmin.slide.updated', HeroSlide::class, $slide->id, [
            'title' => $slide->title,
            'sort_order' => $slide->sort_order,
            'is_active' => $slide->is_active,
        ], $request);

        return redirect()->route('superadmin.marketing.index')->with('success', 'Carousel slide updated.');
    }

    public function destroySlide(HeroSlide $slide)
    {
        $slideId = $slide->id;
        $slideTitle = $slide->title;
        $slide->delete();

        AuditLogger::log('superadmin.slide.deleted', HeroSlide::class, $slideId, [
            'title' => $slideTitle,
        ], request());

        return redirect()->route('superadmin.marketing.index')->with('success', 'Carousel slide removed.');
    }

    public function storeCampaign(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'discount_percent' => 'required|integer|min:1|max:95',
            'max_uses' => 'nullable|integer|min:1|max:1000000',
            'description' => 'nullable|string|max:2000',
            'starts_date' => 'nullable|date',
            'starts_time' => 'nullable|date_format:H:i',
            'ends_date' => 'nullable|date',
            'ends_time' => 'nullable|date_format:H:i',
            'is_active' => 'nullable|boolean',
        ]);

        $startsAt = $this->combineDateAndTime($data['starts_date'] ?? null, $data['starts_time'] ?? null);
        $endsAt = $this->combineDateAndTime($data['ends_date'] ?? null, $data['ends_time'] ?? null);

        if ($startsAt && $endsAt && Carbon::parse($endsAt)->lt(Carbon::parse($startsAt))) {
            return redirect()->back()->withErrors([
                'ends_date' => 'End date/time must be after or equal to the start date/time.',
            ])->withInput();
        }

        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['starts_at'] = $startsAt;
        $data['ends_at'] = $endsAt;
        $data['code'] = $data['code'] ? strtoupper(trim($data['code'])) : null;

        unset($data['starts_date'], $data['starts_time'], $data['ends_date'], $data['ends_time']);

        $campaign = DiscountCampaign::create($data);

        AuditLogger::log('superadmin.campaign.created', DiscountCampaign::class, $campaign->id, [
            'name' => $campaign->name,
            'code' => $campaign->code,
            'discount_percent' => $campaign->discount_percent,
            'max_uses' => $campaign->max_uses,
        ], $request);

        return redirect()->route('superadmin.marketing.index')->with('success', 'Discount campaign created.');
    }

    public function updateCampaign(Request $request, DiscountCampaign $campaign)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'discount_percent' => 'required|integer|min:1|max:95',
            'max_uses' => 'nullable|integer|min:1|max:1000000',
            'description' => 'nullable|string|max:2000',
            'starts_date' => 'nullable|date',
            'starts_time' => 'nullable|date_format:H:i',
            'ends_date' => 'nullable|date',
            'ends_time' => 'nullable|date_format:H:i',
            'is_active' => 'nullable|boolean',
        ]);

        if (isset($data['max_uses']) && $data['max_uses'] !== null && (int) $data['max_uses'] < (int) $campaign->used_count) {
            return redirect()->back()->withErrors([
                'max_uses' => 'Maximum uses cannot be less than the already used count.',
            ])->withInput();
        }

        $startsAt = $this->combineDateAndTime($data['starts_date'] ?? null, $data['starts_time'] ?? null);
        $endsAt = $this->combineDateAndTime($data['ends_date'] ?? null, $data['ends_time'] ?? null);

        if ($startsAt && $endsAt && Carbon::parse($endsAt)->lt(Carbon::parse($startsAt))) {
            return redirect()->back()->withErrors([
                'ends_date' => 'End date/time must be after or equal to the start date/time.',
            ])->withInput();
        }

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['starts_at'] = $startsAt;
        $data['ends_at'] = $endsAt;
        $data['code'] = $data['code'] ? strtoupper(trim($data['code'])) : null;

        unset($data['starts_date'], $data['starts_time'], $data['ends_date'], $data['ends_time']);

        $campaign->update($data);

        AuditLogger::log('superadmin.campaign.updated', DiscountCampaign::class, $campaign->id, [
            'name' => $campaign->name,
            'code' => $campaign->code,
            'discount_percent' => $campaign->discount_percent,
            'max_uses' => $campaign->max_uses,
            'used_count' => $campaign->used_count,
            'is_active' => $campaign->is_active,
        ], $request);

        return redirect()->route('superadmin.marketing.index')->with('success', 'Discount campaign updated.');
    }

    public function destroyCampaign(DiscountCampaign $campaign)
    {
        $campaignId = $campaign->id;
        $campaignMeta = [
            'name' => $campaign->name,
            'code' => $campaign->code,
        ];
        $campaign->delete();

        AuditLogger::log('superadmin.campaign.deleted', DiscountCampaign::class, $campaignId, $campaignMeta, request());

        return redirect()->route('superadmin.marketing.index')->with('success', 'Discount campaign removed.');
    }

    private function combineDateAndTime(?string $date, ?string $time): ?string
    {
        if (! $date) {
            return null;
        }

        $time = $time ?: '00:00';

        return Carbon::createFromFormat('Y-m-d H:i', "{$date} {$time}")->format('Y-m-d H:i:s');
    }
}
