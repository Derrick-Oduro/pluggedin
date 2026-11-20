<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Fills the marketplace with real-world demo listings (specs, prices in GH₵ and
 * free-licence photos from Wikimedia Commons; see demo-images/CREDITS.md).
 *
 * For previewing the site only. It is not part of DatabaseSeeder and refuses to
 * run in production. Re-running updates the same listings instead of duplicating.
 *
 *   php artisan db:seed --class=DemoCatalogSeeder
 *   php artisan catalog:demo-remove        (deletes these listings and photos)
 */
class DemoCatalogSeeder extends Seeder
{
    public const IMAGE_DIR = 'products/demo';

    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->error('The demo catalogue is for local previews only.');

            return;
        }

        // Categories the demo needs, created only if missing (existing ones are left as they are).
        $needed = [
            'laptops' => ['Laptops', 'laptop'], 'desktops' => ['Desktops', 'desktop'], 'phones' => ['Phones', 'phone'],
            'tablets' => ['Tablets', 'tablet'], 'memory' => ['Memory', 'memory'], 'storage' => ['Storage', 'storage'],
            'monitors' => ['Monitors', 'monitor'], 'wifi' => ['Wi-Fi & Networking', 'networking'], 'accessories' => ['Accessories', 'accessory'],
        ];
        foreach ($needed as $slug => [$name, $template]) {
            Category::firstOrCreate(['slug' => $slug], ['name' => $name, 'spec_template' => $template]);
        }

        $categories = Category::pluck('id', 'slug');
        $sourceDir = database_path('seeders/demo-images');
        $created = 0;

        foreach (require database_path('seeders/data/demo_catalog.php') as $item) {
            $categoryId = $categories[$item['category']];

            $images = [];
            foreach ($item['images'] as $stem) {
                $source = collect(File::glob("$sourceDir/$stem.*"))->first();
                if (! $source) {
                    continue;
                }
                $path = self::IMAGE_DIR.'/'.basename($source);
                if (! Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->put($path, File::get($source));
                }
                $images[] = $path;
            }

            $product = Product::updateOrCreate(
                ['name' => $item['name'], 'category_id' => $categoryId],
                [
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'stock_quantity' => $item['stock'],
                    'condition' => $item['condition'],
                    'specs' => array_filter($item['specs'], fn ($v) => $v !== null && $v !== ''),
                    'whats_included' => $item['included'] ?? null,
                    'image_path' => $images[0] ?? null,
                    'images' => $images,
                    'status' => 'approved',
                    'listing_state' => 'active',
                    'is_user_uploaded' => false,
                    'reviewed_at' => now(),
                ]
            );
            $created += $product->wasRecentlyCreated ? 1 : 0;
        }

        $this->command?->info("Demo catalogue ready ($created new listings).");
    }
}
