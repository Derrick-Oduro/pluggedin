<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->string('moderation_status', 20)->default('pending')->after('comment');
            $table->boolean('is_reported')->default(false)->after('moderation_status');
            $table->text('report_reason')->nullable()->after('is_reported');
            $table->text('moderation_note')->nullable()->after('report_reason');
            $table->foreignId('moderated_by')->nullable()->after('moderation_note')->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable()->after('moderated_by');
        });
    }

    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('moderated_by');
            $table->dropColumn([
                'moderation_status',
                'is_reported',
                'report_reason',
                'moderation_note',
                'moderated_at',
            ]);
        });
    }
};
