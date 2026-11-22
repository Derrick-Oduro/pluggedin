<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrdersAndSavedItemsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'user']);
        $this->user = User::factory()->create();
        $this->user->assignRole('user');
        $this->category = Category::create(['name' => 'Laptops', 'slug' => 'laptops']);
    }

    private function product(string $name, int $stock = 5, array $extra = []): Product
    {
        return Product::create($extra + ['name' => $name, 'description' => 'x', 'price' => 1000, 'category_id' => $this->category->id, 'stock_quantity' => $stock, 'status' => 'approved']);
    }

    private function order(string $status, Product $product, array $extra = []): Order
    {
        $order = Order::create($extra + ['user_id' => $this->user->id, 'total_price' => 1000, 'status' => $status, 'fulfilment_method' => 'delivery', 'delivery_phone' => '0200000000']);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => 1, 'price' => 1000]);

        return $order;
    }

    public function test_orders_can_be_filtered_and_show_the_right_actions(): void
    {
        $laptop = $this->product('ThinkPad T480');
        $shipped = $this->order('shipped', $laptop, ['paid_at' => now()]);
        $unpaid = $this->order('pending', $laptop);
        $done = $this->order('completed', $laptop, ['paid_at' => now()]);
        $cancelled = $this->order('cancelled', $laptop);

        $this->actingAs($this->user)->get(route('orders.index'))
            ->assertOk()
            ->assertSee('On the way to you')
            ->assertSee('Waiting for payment')
            ->assertSee('Pay now')
            ->assertSee('ThinkPad T480');

        $active = $this->actingAs($this->user)->get(route('orders.index', ['filter' => 'active']))->assertOk();
        $active->assertSee("Order #{$shipped->id}")->assertSee("Order #{$unpaid->id}")
            ->assertDontSee("Order #{$done->id}")->assertDontSee("Order #{$cancelled->id}");

        $this->actingAs($this->user)->get(route('orders.index', ['filter' => 'cancelled']))
            ->assertSee("Order #{$cancelled->id}")->assertDontSee("Order #{$shipped->id}");

        // Unknown filters fall back to all orders.
        $this->actingAs($this->user)->get(route('orders.index', ['filter' => 'nope']))->assertOk()->assertSee("Order #{$done->id}");
    }

    public function test_saved_items_show_stock_and_only_buyable_items_move_to_cart(): void
    {
        $inStock = $this->product('Galaxy S21', 5);
        $soldOut = $this->product('Pixel 6', 0);
        $paused = $this->product('iPad Air', 4, ['listing_state' => 'paused']);
        foreach ([$inStock, $soldOut, $paused] as $p) {
            WishlistItem::create(['user_id' => $this->user->id, 'product_id' => $p->id]);
        }

        $this->actingAs($this->user)->get(route('wishlist.index'))
            ->assertOk()
            ->assertSee('In stock')
            ->assertSee('Sold out')
            ->assertSee('No longer available');

        // A sold-out item stays saved and is not added.
        $soldOutSave = WishlistItem::where('product_id', $soldOut->id)->first();
        $this->actingAs($this->user)->post(route('wishlist.move-to-cart', $soldOutSave))->assertSessionHas('error');
        $this->assertSame(0, CartItem::count());
        $this->assertNotNull($soldOutSave->fresh());

        // "Add all" only takes the buyable one.
        $this->actingAs($this->user)->post(route('wishlist.move-all-to-cart'))->assertRedirect(route('cart.index'));
        $this->assertSame([$inStock->id], CartItem::pluck('product_id')->all());
        $this->assertSame(2, WishlistItem::count());
    }

    public function test_moving_to_cart_never_exceeds_stock(): void
    {
        $last = $this->product('Last ThinkPad', 1);
        CartItem::create(['user_id' => $this->user->id, 'product_id' => $last->id, 'quantity' => 1]);
        $saved = WishlistItem::create(['user_id' => $this->user->id, 'product_id' => $last->id]);

        $this->actingAs($this->user)->post(route('wishlist.move-to-cart', $saved))->assertSessionHas('success');

        $this->assertSame(1, CartItem::first()->quantity);
    }
}
