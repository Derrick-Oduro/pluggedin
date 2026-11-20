<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // 'paystack' (paid online before fulfilment) or 'cash_on_delivery' (paid when handed over).
            $table->string('payment_method', 30)->default('paystack')->after('status');
            $table->timestamp('paid_at')->nullable()->after('payment_method');
        });

        // Orders already paid through Paystack.
        DB::table('orders')
            ->whereIn('id', DB::table('payment_transactions')->where('status', 'paid')->select('order_id'))
            ->update(['paid_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'paid_at']);
        });
    }
};
