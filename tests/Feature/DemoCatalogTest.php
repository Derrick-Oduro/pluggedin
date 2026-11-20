<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\CategorySeeder;
use Database\Seeders\DemoCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DemoCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_catalogue_seeds_listings_with_photos_and_can_be_removed(): void
    {
        Storage::fake('public');
        $this->seed(CategorySeeder::class);

        $this->seed(DemoCatalogSeeder::class);
        $this->seed(DemoCatalogSeeder::class); // re-running must not duplicate

        $names = collect(require database_path('seeders/data/demo_catalog.php'))->pluck('name');
        $this->assertSame($names->count(), Product::approved()->whereIn('name', $names)->count());

        $laptop = Product::where('name', 'like', 'Lenovo ThinkPad T480%')->firstOrFail();
        $this->assertSame('Intel Core i5-8350U', $laptop->specs['processor']);
        $this->get(route('products.show', $laptop))->assertOk()->assertSee('Intel Core i5-8350U');
        $this->get(route('products.index'))->assertOk();

        $withPhoto = Product::whereIn('name', $names)->whereNotNull('image_path')->firstOrFail();
        Storage::disk('public')->assertExists($withPhoto->image_path);

        $this->artisan('catalog:demo-remove')->assertSuccessful();
        $this->assertSame(0, Product::whereIn('name', $names)->count());
        $this->assertTrue(Product::count() > 0, 'Non-demo products are kept');
        Storage::disk('public')->assertMissing($withPhoto->image_path);
    }
}
