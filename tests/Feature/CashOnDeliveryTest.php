<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CashOnDeliveryTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'admin']);
        config(['services.paystack.secret_key' => 'sk_test_x']);
        Http::fake(); // pay on delivery must never call Paystack

        $this->buyer = User::factory()->create();
        $this->buyer->assignRole('user');
        $category = Category::create(['name' => 'Storage', 'slug' => 'storage']);
        $this->product = Product::create(['name' => 'Samsung SSD', 'description' => 'Fast', 'price' => 100, 'category_id' => $category->id, 'stock_quantity' => 5, 'status' => 'approved']);
    }

    private function placeCashOrder(array $overrides = [])
    {
        CartItem::create(['user_id' => $this->buyer->id, 'product_id' => $this->product->id, 'quantity' => 2]);

        return $this->actingAs($this->buyer)->post(route('orders.store'), $overrides + [
            'fulfilment_method' => 'delivery',
            'delivery_address' => 'Commonwealth Hall',
            'delivery_phone' => '0594042521',
            'payment_method' => 'cash_on_delivery',
        ]);
    }

    public function test_pay_on_delivery_order_is_confirmed_without_paystack(): void
    {
        $this->actingAs($this->buyer);
        CartItem::create(['user_id' => $this->buyer->id, 'product_id' => $this->product->id, 'quantity' => 1]);
        $this->get(route('checkout'))->assertOk()->assertSee('Pay on delivery');
        CartItem::query()->delete();

        $order = null;
        $response = $this->placeCashOrder();
        $order = Order::firstOrFail();

        $response->assertRedirect(route('orders.show', $order))->assertSessionHas('success');
        Http::assertNothingSent();
        $this->assertSame('confirmed', $order->status);
        $this->assertSame(Order::CASH_ON_DELIVERY, $order->payment_method);
        $this->assertNull($order->paid_at);
        $this->assertSame(3, $this->product->fresh()->stock_quantity);
        $this->assertSame(0, $this->buyer->cartItems()->count());

        $this->get(route('orders.show', $order))->assertOk()->assertSee('Pay on delivery: GH₵200.00')->assertSee('Cancel order');

        // The unpaid-order expiry job must leave it alone.
        $this->travel(3)->hours();
        Artisan::call('orders:expire-unpaid');
        $this->assertSame('confirmed', $order->fresh()->status);
    }

    public function test_buyer_can_cancel_unpaid_pay_on_delivery_order_and_stock_returns(): void
    {
        $this->placeCashOrder();
        $order = Order::firstOrFail();

        $this->actingAs($this->buyer)->patch(route('orders.cancel', $order))->assertRedirect(route('orders.show', $order));

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame(5, $this->product->fresh()->stock_quantity);
    }

    public function test_admin_must_record_cash_before_completing(): void
    {
        $this->placeCashOrder();
        $order = Order::firstOrFail();
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        foreach (['processing', 'shipped'] as $status) {
            $this->actingAs($admin)->patch(route('admin.orders.updateStatus', $order), ['status' => $status]);
        }
        $this->actingAs($admin)->patch(route('admin.orders.updateStatus', $order), ['status' => 'completed'])->assertSessionHas('error');
        $this->assertSame('shipped', $order->fresh()->status);

        $this->actingAs($admin)->get(route('admin.orders.show', $order))->assertOk()->assertSee('Cash received');

        $this->actingAs($admin)->patch(route('admin.orders.cashReceived', $order), ['collection_method' => 'mobile_money', 'collection_reference' => 'MP123'])
            ->assertSessionHas('success');
        $this->assertNotNull($order->fresh()->paid_at);
        $this->assertSame(1, PaymentTransaction::where('order_id', $order->id)->where('status', 'paid')->where('provider', 'mobile_money')->count());

        // Recording twice does nothing.
        $this->actingAs($admin)->patch(route('admin.orders.cashReceived', $order), ['collection_method' => 'cash'])->assertSessionHas('error');
        $this->assertSame(1, PaymentTransaction::where('order_id', $order->id)->count());

        $this->actingAs($admin)->patch(route('admin.orders.updateStatus', $order), ['status' => 'completed'])->assertSessionHas('success');
        $this->assertSame('completed', $order->fresh()->status);

        // Once paid, the buyer can no longer cancel it themselves.
        $this->actingAs($this->buyer)->patch(route('orders.cancel', $order))->assertStatus(422);
    }

    public function test_open_pay_on_delivery_orders_are_limited(): void
    {
        config(['marketplace.cash_on_delivery_open_limit' => 1]);
        $this->placeCashOrder();

        $this->placeCashOrder()->assertRedirect(route('checkout'))->assertSessionHasErrors('payment_method');
        $this->assertSame(1, Order::count());
        $this->assertSame(1, $this->buyer->cartItems()->count(), 'Cart is kept so they can pay online instead');
    }

    public function test_pay_on_delivery_can_be_switched_off(): void
    {
        config(['marketplace.cash_on_delivery' => false]);

        $this->placeCashOrder()->assertSessionHasErrors('payment_method');
        $this->assertSame(0, Order::count());
    }
}
