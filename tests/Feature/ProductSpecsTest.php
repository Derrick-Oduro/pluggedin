<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductSpecsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'user']);
    }

    private function laptopPayload(Category $category, array $specs = []): array
    {
        return [
            'name' => 'HP EliteBook 840 G8',
            'description' => 'Clean laptop, light use.',
            'price' => 4500,
            'category_id' => $category->id,
            'stock_quantity' => 1,
            'condition' => 'like_new',
            'whats_included' => 'Laptop and charger',
            'specs' => array_merge([
                'brand' => 'HP',
                'model' => 'EliteBook 840 G8',
                'processor' => 'Intel Core i5-1135G7',
                'ram_size' => '16',
                'storage_size' => '512 GB',
                'storage_type' => 'NVMe SSD',
                'graphics' => 'Intel Iris Xe',
                'screen_size' => '14',
                'charger_included' => 'Yes, original',
                'operating_system' => 'Windows 11',
                'battery_health' => '88',
            ], $specs),
        ];
    }

    public function test_laptop_upload_requires_core_laptop_specs(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $category = Category::create(['name' => 'Laptops', 'slug' => 'laptops']);

        $payload = $this->laptopPayload($category);
        unset($payload['specs']['processor'], $payload['specs']['ram_size']);

        $this->actingAs($user)->post(route('products.upload.store'), $payload)
            ->assertSessionHasErrors(['specs.processor', 'specs.ram_size']);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_laptop_specs_are_saved_and_shown_on_product_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $category = Category::create(['name' => 'Laptops', 'slug' => 'laptops']);

        $this->actingAs($user)->post(route('products.upload.store'), $this->laptopPayload($category, [
            'not_a_real_field' => 'should be dropped',
        ]))->assertRedirect(route('user.dashboard'));

        $product = Product::firstOrFail();
        $this->assertSame('like_new', $product->condition);
        $this->assertSame('Intel Core i5-1135G7', $product->specs['processor']);
        $this->assertArrayNotHasKey('not_a_real_field', $product->specs);

        $product->update(['status' => 'approved']);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('Specifications')
            ->assertSee('Intel Core i5-1135G7')
            ->assertSee('16 GB')
            ->assertSee('88%')
            ->assertSee('Used - like new');
    }

    public function test_product_page_shows_highlights_condition_notes_and_box_contents(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $category = Category::create(['name' => 'Laptops', 'slug' => 'laptops']);

        $this->actingAs($user)->post(route('products.upload.store'), $this->laptopPayload($category, [
            'known_issues' => 'Small scuff on the lid',
        ]))->assertRedirect();

        $product = Product::firstOrFail();
        $product->update(['status' => 'approved']);

        $highlights = \App\Support\ProductSpecs::highlights($product->category, $product->specs);
        $this->assertSame(['label' => 'Storage capacity', 'value' => '512 GB NVMe SSD'], $highlights[2]);

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('512 GB NVMe SSD')
            ->assertSee("Seller's condition notes", false)
            ->assertSee('Small scuff on the lid')
            ->assertSee('Laptop and charger')
            ->assertSee('More from seller');
    }

    public function test_spec_fields_follow_the_selected_category(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        $memory = Category::create(['name' => 'Memory', 'slug' => 'memory']);

        // Laptop-only fields are not required for RAM sticks; RAM fields are.
        $this->actingAs($user)->post(route('products.upload.store'), [
            'name' => 'Kingston 16GB DDR4',
            'description' => 'Laptop RAM',
            'price' => 300,
            'category_id' => $memory->id,
            'stock_quantity' => 2,
            'condition' => 'new',
            'specs' => ['brand' => 'Kingston', 'capacity' => '16 GB', 'memory_type' => 'DDR4', 'form_factor' => 'SO-DIMM (laptop)', 'speed' => '3200'],
        ])->assertSessionHasNoErrors();

        $this->assertSame('DDR4', Product::firstOrFail()->specs['memory_type']);
    }
}
