<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $fillable = [
        'uploaded_by',
        'shop_id',
        'name',
        'description',
        'price',
        'category_id',
        'stock_quantity',
        'image_path',
        'status',
        'listing_state',
        'admin_review_comment',
        'reviewed_by',
        'reviewed_at',
        'is_user_uploaded',
        'images',
        'condition',
        'specs',
        'whats_included',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'is_user_uploaded' => 'boolean',
        'images' => 'array',
        'specs' => 'array',
    ];

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved')->where('listing_state', 'active');
    }

    /** All image paths, falling back to the single legacy image_path. */
    public function galleryImages(): array
    {
        $images = array_values(array_filter($this->images ?: []));

        return $images ?: array_values(array_filter([$this->image_path]));
    }

    public function conditionLabel(): ?string
    {
        return \App\Support\ProductSpecs::conditionLabel($this->condition);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function referralLinks(): HasMany
    {
        return $this->hasMany(ReferralLink::class);
    }
}
