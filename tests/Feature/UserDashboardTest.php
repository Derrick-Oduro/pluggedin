<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_user_sees_empty_states(): void
    {
        Role::firstOrCreate(['name' => 'user']);
        $user = User::factory()->create(['name' => 'Ama Mensah']);
        $user->assignRole('user');

        $this->actingAs($user)->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('Hey, Ama.')
            ->assertSee('No orders yet')
            ->assertDontSee('Needs your attention');
    }

    public function test_dashboard_lists_things_that_need_attention(): void
    {
        Role::firstOrCreate(['name' => 'user']);
        $user = User::factory()->create(['name' => 'Kofi Boateng']);
        $user->assignRole('user');
        $category = Category::create(['name' => 'Laptops', 'slug' => 'laptops']);
        $product = Product::create(['name' => 'ThinkPad T480', 'description' => 'x', 'price' => 2500, 'category_id' => $category->id, 'stock_quantity' => 2, 'status' => 'approved']);

        $unpaid = Order::create(['user_id' => $user->id, 'total_price' => 2500, 'status' => 'pending', 'payment_method' => 'paystack', 'fulfilment_method' => 'delivery', 'delivery_phone' => '0200000000']);
        OrderItem::create(['order_id' => $unpaid->id, 'product_id' => $product->id, 'quantity' => 1, 'price' => 2500]);
        $cash = Order::create(['user_id' => $user->id, 'total_price' => 1200, 'status' => 'confirmed', 'payment_method' => 'cash_on_delivery', 'fulfilment_method' => 'campus_pickup', 'delivery_phone' => '0200000000']);
        OrderItem::create(['order_id' => $cash->id, 'product_id' => $product->id, 'quantity' => 1, 'price' => 1200]);
        OrderStatusHistory::create(['order_id' => $cash->id, 'from_status' => 'pending', 'to_status' => 'confirmed']);
        Product::create(['name' => 'Old Mouse', 'description' => 'x', 'price' => 50, 'category_id' => $category->id, 'stock_quantity' => 1, 'status' => 'rejected', 'uploaded_by' => $user->id, 'is_user_uploaded' => true, 'admin_review_comment' => 'Photos are blurry']);

        $this->actingAs($user)->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('Needs your attention')
            ->assertSee("Order #{$unpaid->id} is waiting for payment")
            ->assertSee('Have GH₵1,200.00 ready at pickup')
            ->assertSee('Photos are blurry')
            ->assertSee('Rate what you bought')       // confirmed order with an unreviewed item
            ->assertSee("Order #{$cash->id}</b> is now <b class=\"text-stone-950\">confirmed", false)
            ->assertSee('ThinkPad T480');
    }
}
