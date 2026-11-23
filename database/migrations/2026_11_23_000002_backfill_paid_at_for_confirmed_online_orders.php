<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Online orders confirmed before paid_at existed (or confirmed by an admin by hand) have no
 * paid date, so they showed as "not paid yet" and were left out of "Money in". An online order
 * only moves past "pending" once it is paid, so use the order date for those.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')
            ->where('payment_method', 'paystack')
            ->whereNull('paid_at')
            ->whereIn('status', ['confirmed', 'processing', 'ready_for_pickup', 'shipped', 'completed', 'refunded'])
            ->update(['paid_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        // Not reversible: we can't tell backfilled dates from real ones.
    }
};
