<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total_price',
        'subtotal_price',
        'discount_amount',
        'discount_campaign_id',
        'applied_discount_code',
        'status',
        'delivery_address',
        'delivery_phone'
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'subtotal_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function discountCampaign(): BelongsTo
    {
        return $this->belongsTo(DiscountCampaign::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }
}
