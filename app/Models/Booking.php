<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'service_id',
        'device_model',
        'preferred_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'preferred_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class)->latest();
    }
}
