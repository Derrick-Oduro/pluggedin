<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('discount_campaigns', function (Blueprint $table) {
            $table->unsignedInteger('max_uses')->nullable()->after('discount_percent');
            $table->unsignedInteger('used_count')->default(0)->after('max_uses');
        });
    }

    public function down(): void
    {
        Schema::table('discount_campaigns', function (Blueprint $table) {
            $table->dropColumn(['max_uses', 'used_count']);
        });
    }
};
