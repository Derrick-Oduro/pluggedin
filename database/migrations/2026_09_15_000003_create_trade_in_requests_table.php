<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trade_in_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('desired_product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('device_type');
            $table->string('brand');
            $table->string('model');
            $table->string('specifications')->nullable();
            $table->string('condition');
            $table->text('notes')->nullable();
            $table->json('images')->nullable();
            $table->enum('status', ['submitted', 'under_review', 'quoted', 'accepted', 'declined', 'completed'])->default('submitted');
            $table->decimal('quoted_value', 10, 2)->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamp('quoted_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('trade_in_requests'); }
};
