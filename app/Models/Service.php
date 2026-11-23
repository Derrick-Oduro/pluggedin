<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** A workshop job customers can book: a repair, an upgrade, or a diagnosis. */
class Service extends Model
{
    public const TYPES = [
        'repair' => 'Repair',
        'upgrade' => 'Upgrade',
        'diagnosis' => 'Diagnosis',
    ];

    public const DEVICE_TYPES = [
        'laptop' => 'Laptop',
        'desktop' => 'Desktop',
        'phone' => 'Phone',
        'tablet' => 'Tablet',
    ];

    protected $fillable = [
        'name', 'type', 'description', 'price', 'price_is_from', 'turnaround',
        'device_types', 'parts_category_id', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'price_is_from' => 'boolean',
        'device_types' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /** "GH₵250.00", "From GH₵250.00" or "Free". */
    public function priceLabel(): string
    {
        if ((float) $this->price <= 0) {
            return 'Free';
        }

        return ($this->price_is_from ? 'From ' : '').'GH₵'.number_format((float) $this->price, 2);
    }

    /** "Laptops & phones", or null when it suits any device. */
    public function devicesLabel(): ?string
    {
        $types = collect($this->device_types ?? [])->map(fn ($t) => strtolower(self::DEVICE_TYPES[$t] ?? $t).'s')->values();
        if ($types->isEmpty() || $types->count() === count(self::DEVICE_TYPES)) {
            return null;
        }

        return ucfirst($types->count() > 1 ? $types->slice(0, -1)->implode(', ').' & '.$types->last() : $types->first());
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function partsCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parts_category_id');
    }
}
