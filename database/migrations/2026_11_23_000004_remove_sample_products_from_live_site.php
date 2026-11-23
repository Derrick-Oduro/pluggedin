<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Removes the sample products that older versions of the seeders put on the live site
 * (the 8 starter products, plus any demo-catalogue listings). Real listings are never
 * touched: only products added by PluggedIn itself (not uploaded by a member) whose name
 * matches a sample are affected.
 *
 * Deleting a product also deletes its order lines, so samples that appear in an order
 * are hidden instead, to keep order history intact.
 *
 * Local and test databases are skipped so the demo catalogue keeps working there.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (app()->environment(['local', 'testing'])) {
            return;
        }

        $names = [
            'Samsung 980 PRO SSD 1TB', 'WD Blue SSD 500GB', 'Crucial MX500 2TB',
            'Corsair Vengeance 16GB (2x8GB) DDR4', 'Kingston Fury 32GB (2x16GB) DDR4', 'G.Skill Trident Z5 16GB DDR5',
            'SSD Installation Kit', 'Thermal Paste Premium',
        ];
        $demoFile = database_path('seeders/data/demo_catalog.php');
        if (is_file($demoFile)) {
            $names = array_merge($names, array_column(require $demoFile, 'name'));
        }

        $samples = DB::table('products')
            ->whereIn('name', $names)
            ->where(fn ($q) => $q->where('is_user_uploaded', false)->orWhereNull('is_user_uploaded'))
            ->whereNull('uploaded_by')
            ->pluck('id');

        if ($samples->isEmpty()) {
            return;
        }

        $ordered = DB::table('order_items')->whereIn('product_id', $samples)->distinct()->pluck('product_id');

        DB::table('products')->whereIn('id', $ordered)->update([
            'status' => 'rejected',
            'admin_review_comment' => 'Sample product removed from the live site.',
            'stock_quantity' => 0,
        ]);
        DB::table('products')->whereIn('id', $samples->diff($ordered))->delete();
    }

    public function down(): void
    {
        // Nothing to restore: sample data isn't brought back.
    }
};
