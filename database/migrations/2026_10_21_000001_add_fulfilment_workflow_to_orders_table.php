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
            $table->timestamp('cancelled_at')->nullable()->after('status');
            $table->foreignId('cancelled_by')->nullable()->after('cancelled_at')->constrained('users')->nullOnDelete();
            $table->string('cancellation_reason', 500)->nullable()->after('cancelled_by');
            $table->string('tracking_number', 160)->nullable()->after('cancellation_reason');
            $table->timestamp('shipped_at')->nullable()->after('tracking_number');
            $table->timestamp('completed_at')->nullable()->after('shipped_at');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE orders ALTER COLUMN status TYPE VARCHAR(40) USING status::VARCHAR");
        } elseif (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY status VARCHAR(40) NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        DB::table('orders')
            ->whereIn('status', ['processing', 'ready_for_pickup', 'shipped', 'refunded'])
            ->update(['status' => 'cancelled']);

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropColumn([
                'cancelled_at',
                'cancelled_by',
                'cancellation_reason',
                'tracking_number',
                'shipped_at',
                'completed_at',
            ]);
        });
    }
};
