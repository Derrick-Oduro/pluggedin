<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::table('users', fn (Blueprint $table) => $table->timestamp('seller_verified_at')->nullable()->after('email_verified_at')); Schema::create('product_reports', function (Blueprint $table) { $table->id(); $table->foreignId('product_id')->constrained()->cascadeOnDelete(); $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete(); $table->string('reason'); $table->text('details')->nullable(); $table->enum('status',['open','resolved','dismissed'])->default('open'); $table->timestamps(); $table->unique(['product_id','reporter_id']); }); } public function down(): void { Schema::dropIfExists('product_reports'); Schema::table('users', fn (Blueprint $table) => $table->dropColumn('seller_verified_at')); } };
