<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CheckoutRecoveryTest extends TestCase
{
    use RefreshDatabase;

    private User $buyer;

    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'user']);
        config(['services.paystack.secret_key' => 'sk_test_x']);

        $this->buyer = User::factory()->create();
        $this->buyer->assignRole('user');
        $category = Category::create(['name' => 'Storage', 'slug' => 'storage']);
        $this->product = Product::create(['name' => 'Samsung SSD', 'description' => 'Fast', 'price' => 100, 'category_id' => $category->id, 'stock_quantity' => 3, 'status' => 'approved']);
    }

    private function checkout()
    {
        return $this->actingAs($this->buyer)->post(route('orders.store'), [
            'fulfilment_method' => 'delivery',
            'delivery_address' => 'Commonwealth Hall',
            'delivery_phone' => '0594042521',
        ]);
    }

    private function fakeInitialize(): void
    {
        Http::fake(['*/transaction/initialize' => Http::response(['status' => true, 'data' => ['authorization_url' => 'https://checkout.paystack.test/abc', 'access_code' => 'x']])]);
    }

    public function test_cart_is_kept_when_paystack_refuses_to_start(): void
    {
        CartItem::create(['user_id' => $this->buyer->id, 'product_id' => $this->product->id, 'quantity' => 2]);
        Http::fake(['*/transaction/initialize' => Http::response(['status' => false, 'message' => 'Invalid key'], 401)]);

        $this->checkout()->assertRedirect(route('checkout'))->assertSessionHasErrors('payment');

        $this->assertSame(1, $this->buyer->cartItems()->count());
        $this->assertSame(3, $this->product->fresh()->stock_quantity);
        $this->assertSame('cancelled', Order::firstOrFail()->status);
        $this->followingRedirects()->actingAs($this->buyer)->get(route('checkout'))->assertOk();
    }

    public function test_paystack_being_unreachable_shows_a_message_not_an_error_page(): void
    {
        CartItem::create(['user_id' => $this->buyer->id, 'product_id' => $this->product->id, 'quantity' => 1]);
        Http::fake(fn () => throw new ConnectionException('timed out'));

        $this->checkout()->assertRedirect(route('checkout'))->assertSessionHasErrors('payment');
        $this->assertSame(1, $this->buyer->cartItems()->count());
        $this->assertSame(3, $this->product->fresh()->stock_quantity);
    }

    public function test_cart_is_cleared_only_once_paystack_accepts(): void
    {
        CartItem::create(['user_id' => $this->buyer->id, 'product_id' => $this->product->id, 'quantity' => 1]);
        $this->fakeInitialize();

        $this->checkout()->assertRedirect('https://checkout.paystack.test/abc');
        $this->assertSame(0, $this->buyer->cartItems()->count());
        $this->assertSame(2, $this->product->fresh()->stock_quantity);
    }

    public function test_sold_out_item_error_is_shown_on_checkout_page(): void
    {
        CartItem::create(['user_id' => $this->buyer->id, 'product_id' => $this->product->id, 'quantity' => 1]);
        $this->product->update(['stock_quantity' => 0]);

        $this->checkout()->assertSessionHasErrors('cart');
        $this->actingAs($this->buyer)->get(route('checkout'))
            ->assertSee('Samsung SSD has just sold out', false);
        $this->assertSame(0, Order::count());
    }

    public function test_abandoned_payment_returns_to_order_with_pay_now(): void
    {
        CartItem::create(['user_id' => $this->buyer->id, 'product_id' => $this->product->id, 'quantity' => 1]);
        $this->fakeInitialize();
        $this->checkout();
        $payment = PaymentTransaction::firstOrFail();

        Http::fake(['*/transaction/verify/*' => Http::response(['status' => true, 'data' => ['status' => 'abandoned', 'amount' => 10000]])]);

        $this->actingAs($this->buyer)->get(route('payments.paystack.callback', ['reference' => $payment->reference]))
            ->assertRedirect(route('orders.show', $payment->order_id))
            ->assertSessionHas('error');

        $this->actingAs($this->buyer)->get(route('orders.show', $payment->order_id))
            ->assertSee('Pay now')
            ->assertSee('Waiting for payment');
    }

    public function test_customer_can_retry_payment_for_unpaid_order(): void
    {
        CartItem::create(['user_id' => $this->buyer->id, 'product_id' => $this->product->id, 'quantity' => 1]);
        $this->fakeInitialize();
        $this->checkout();
        $order = Order::firstOrFail();

        $this->actingAs($this->buyer)->post(route('orders.pay', $order))->assertRedirect('https://checkout.paystack.test/abc');
        $this->assertSame(2, PaymentTransaction::where('order_id', $order->id)->count());

        $order->update(['status' => 'cancelled']);
        $this->actingAs($this->buyer)->post(route('orders.pay', $order))->assertRedirect(route('orders.show', $order));

        $stranger = User::factory()->create();
        $this->actingAs($stranger)->post(route('orders.pay', $order))->assertForbidden();
    }

    public function test_cart_refuses_unavailable_items_and_caps_quantity(): void
    {
        $this->actingAs($this->buyer)->post(route('cart.add', $this->product), ['quantity' => 5])->assertSessionHas('success');
        $this->assertSame(3, $this->buyer->cartItems()->first()->quantity);

        $this->product->update(['stock_quantity' => 0]);
        $this->actingAs($this->buyer)->post(route('cart.add', $this->product), ['quantity' => 1])->assertSessionHas('error');

        $hidden = $this->product->replicate()->fill(['name' => 'Hidden', 'status' => 'pending', 'stock_quantity' => 5]);
        $hidden->save();
        $this->actingAs($this->buyer)->post(route('cart.add', $hidden), ['quantity' => 1])->assertSessionHas('error');
        $this->assertSame(0, $this->buyer->cartItems()->where('product_id', $hidden->id)->count());

        $this->actingAs($this->buyer)->get(route('cart.index'))
            ->assertSee('Sold out. Please remove it.')
            ->assertSee('Fix the items marked in red');
    }

    public function test_phone_menu_has_account_and_selling_links(): void
    {
        $this->get('/')->assertSee('aria-label="Menu"', false)->assertSee('Trade in a device')->assertSee('Join PluggedIn');

        $this->actingAs($this->buyer)->get('/')
            ->assertSee('My orders')
            ->assertSee('My listings')
            ->assertSee('Log out');
    }
}
