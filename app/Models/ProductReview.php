<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductReview extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'rating',
        'comment',
        'moderation_status',
        'is_reported',
        'report_reason',
        'moderation_note',
        'moderated_by',
        'moderated_at',
    ];

    protected $casts = [
        'is_reported' => 'boolean',
        'moderated_at' => 'datetime',
    ];

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('moderation_status', 'approved');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderated_by');
    }
}
