<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountCampaign extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'discount_percent',
        'max_uses',
        'used_count',
        'description',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'max_uses' => 'integer',
        'used_count' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        $now = now();

        return $query
            ->where('is_active', true)
            ->where(function (Builder $query) use ($now) {
                $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function (Builder $query) use ($now) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->where(function (Builder $query) {
                $query->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses');
            });
    }

    /** Labels and badge colours for each state(). */
    public const STATES = [
        'live' => ['Live', 'bg-emerald-100 text-emerald-800'],
        'scheduled' => ['Scheduled', 'bg-sky-100 text-sky-800'],
        'paused' => ['Paused', 'bg-amber-100 text-amber-800'],
        'used_up' => ['Used up', 'bg-stone-200 text-stone-700'],
        'ended' => ['Ended', 'bg-stone-200 text-stone-700'],
    ];

    /** Where the campaign is right now: live, scheduled, paused, used_up or ended. */
    public function state(): string
    {
        $now = now();

        return match (true) {
            $this->ends_at !== null && $this->ends_at->lt($now) => 'ended',
            $this->max_uses !== null && $this->used_count >= $this->max_uses => 'used_up',
            ! $this->is_active => 'paused',
            $this->starts_at !== null && $this->starts_at->gt($now) => 'scheduled',
            default => 'live',
        };
    }

    /** No code: applied to every checkout automatically while live. */
    public function isAutomatic(): bool
    {
        return $this->code === null || $this->code === '';
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
