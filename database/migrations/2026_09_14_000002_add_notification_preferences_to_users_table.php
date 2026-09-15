<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notify_order_updates')->default(true)->after('remember_token');
            $table->boolean('notify_booking_updates')->default(true)->after('notify_order_updates');
            $table->boolean('notify_product_updates')->default(true)->after('notify_booking_updates');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['notify_order_updates', 'notify_booking_updates', 'notify_product_updates']);
        });
    }
};
