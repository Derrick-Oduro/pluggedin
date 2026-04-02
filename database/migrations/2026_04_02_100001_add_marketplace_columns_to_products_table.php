<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('uploaded_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->after('image_path');
            $table->text('admin_review_comment')->nullable()->after('status');
            $table->foreignId('reviewed_by')->nullable()->after('admin_review_comment')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->boolean('is_user_uploaded')->default(false)->after('reviewed_at');
            $table->json('images')->nullable()->after('is_user_uploaded');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('uploaded_by');
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn([
                'status',
                'admin_review_comment',
                'reviewed_at',
                'is_user_uploaded',
                'images',
            ]);
        });
    }
};
