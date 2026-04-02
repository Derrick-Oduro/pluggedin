<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('subtotal_price', 10, 2)->nullable()->after('total_price');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('subtotal_price');
            $table->foreignId('discount_campaign_id')->nullable()->after('discount_amount')->constrained('discount_campaigns')->nullOnDelete();
            $table->string('applied_discount_code')->nullable()->after('discount_campaign_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('discount_campaign_id');
            $table->dropColumn(['subtotal_price', 'discount_amount', 'applied_discount_code']);
        });
    }
};
