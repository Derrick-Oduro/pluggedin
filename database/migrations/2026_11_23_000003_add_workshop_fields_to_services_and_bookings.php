<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Turns "services" into a proper workshop menu (repairs, upgrades, diagnosis) that
 * super admins can edit, and gives bookings what the workshop needs to act on them.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('type', 20)->default('upgrade')->after('name');          // repair | upgrade | diagnosis
            $table->boolean('price_is_from')->default(false)->after('price');        // "from GH₵…" when it depends on the device
            $table->string('turnaround', 60)->nullable()->after('price_is_from');   // e.g. "Same day", "1–2 days"
            $table->json('device_types')->nullable()->after('turnaround');          // laptop, desktop, phone, tablet
            $table->foreignId('parts_category_id')->nullable()->after('device_types')->constrained('categories')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('parts_category_id');
            $table->unsignedInteger('sort_order')->default(0)->after('is_active');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('device_type', 20)->nullable()->after('service_id');
            $table->string('contact_phone', 40)->nullable()->after('device_model');
            $table->string('handover_method', 20)->default('drop_off')->after('contact_phone'); // drop_off | pickup
            $table->string('pickup_location')->nullable()->after('handover_method');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['device_type', 'contact_phone', 'handover_method', 'pickup_location']);
        });
        Schema::table('services', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parts_category_id');
            $table->dropColumn(['type', 'price_is_from', 'turnaround', 'device_types', 'is_active', 'sort_order']);
        });
    }
};
