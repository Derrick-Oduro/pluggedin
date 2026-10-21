<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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
        'fulfilment_method',
        'pickup_campus',
        'delivery_address',
        'delivery_phone',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'tracking_number',
        'shipped_at',
        'completed_at',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'subtotal_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
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

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function cancelAndRestoreInventory(?int $cancelledBy, ?string $reason = null): bool
    {
        if (in_array($this->status, ['cancelled', 'refunded'], true)) {
            return false;
        }

        return DB::transaction(function () use ($cancelledBy, $reason) {
            $order = static::query()->lockForUpdate()->findOrFail($this->id);

            if (in_array($order->status, ['cancelled', 'refunded'], true)) {
                return false;
            }

            foreach ($order->items()->with('product')->get() as $item) {
                $item->product?->increment('stock_quantity', $item->quantity);
            }

            $previousStatus = $order->status;
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => $cancelledBy,
                'cancellation_reason' => $reason,
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'changed_by' => $cancelledBy,
                'from_status' => $previousStatus,
                'to_status' => 'cancelled',
            ]);

            $this->refresh();

            return true;
        });
    }
}
