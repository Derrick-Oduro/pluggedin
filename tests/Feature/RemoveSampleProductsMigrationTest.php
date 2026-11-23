<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemoveSampleProductsMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_live_site_loses_sample_products_but_keeps_real_ones_and_order_history(): void
    {
        $category = Category::create(['name' => 'Storage', 'slug' => 'storage']);
        $member = User::factory()->create();
        $make = fn (string $name, array $extra = []) => Product::create($extra + ['name' => $name, 'description' => 'x', 'price' => 100, 'category_id' => $category->id, 'stock_quantity' => 5, 'status' => 'approved']);

        $unordered = $make('WD Blue SSD 500GB');
        $ordered = $make('Samsung 980 PRO SSD 1TB');
        $memberListing = $make('Crucial MX500 2TB', ['uploaded_by' => $member->id, 'is_user_uploaded' => true]);
        $real = $make('Kingston A400 240GB');

        $order = Order::create(['user_id' => $member->id, 'total_price' => 100, 'status' => 'completed', 'fulfilment_method' => 'delivery', 'delivery_phone' => '0200000000']);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $ordered->id, 'quantity' => 1, 'price' => 100]);

        $this->app['env'] = 'production';
        (require database_path('migrations/2026_11_23_000004_remove_sample_products_from_live_site.php'))->up();

        $this->assertModelMissing($unordered);
        $this->assertSame('rejected', $ordered->fresh()->status, 'Ordered samples are hidden, not deleted');
        $this->assertSame(1, $order->items()->count(), 'Order history is kept');
        $this->assertSame('approved', $memberListing->fresh()->status, 'A member listing with a sample name is untouched');
        $this->assertModelExists($real);
    }
}
