<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('condition', 30)->nullable()->after('stock_quantity');
            $table->json('specs')->nullable()->after('condition');
            $table->text('whats_included')->nullable()->after('specs');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('spec_template', 40)->nullable()->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['condition', 'specs', 'whats_included']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('spec_template');
        });
    }
};
