<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('campus')->nullable();
            $table->string('phone')->nullable();
            $table->string('logo_path')->nullable();
            $table->enum('plan', ['starter', 'campus_pro', 'business'])->default('starter');
            $table->enum('status', ['pending', 'approved', 'suspended'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
        Schema::table('products', function (Blueprint $table) { $table->foreignId('shop_id')->nullable()->after('uploaded_by')->constrained()->nullOnDelete(); });
    }
    public function down(): void { Schema::table('products', fn (Blueprint $table) => $table->dropConstrainedForeignId('shop_id')); Schema::dropIfExists('shops'); }
};
