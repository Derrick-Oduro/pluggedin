<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TradeInRequest extends Model
{
    protected $fillable = ['user_id', 'desired_product_id', 'device_type', 'brand', 'model', 'specifications', 'condition', 'notes', 'images', 'status', 'quoted_value', 'admin_notes', 'quoted_at'];
    protected $casts = ['images' => 'array', 'quoted_at' => 'datetime', 'quoted_value' => 'decimal:2'];
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function desiredProduct(): BelongsTo { return $this->belongsTo(Product::class, 'desired_product_id'); }
}
