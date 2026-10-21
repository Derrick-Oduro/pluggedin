<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\CartItem;
use App\Models\PaymentTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrderFulfilmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cancellation_restores_stock_once(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Fulfilment', 'slug' => 'fulfilment']);
        $product = Product::create([
            'name' => 'Fulfilment Product',
            'description' => 'Product used for fulfilment tests',
            'price' => 50,
            'category_id' => $category->id,
            'stock_quantity' => 0,
            'status' => 'approved',
        ]);
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => 50,
            'status' => 'pending',
            'fulfilment_method' => 'delivery',
            'delivery_address' => 'Test address',
            'delivery_phone' => '1234567890',
        ]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 25,
        ]);

        $this->actingAs($user)
            ->patch(route('orders.cancel', $order), ['reason' => 'Changed my mind'])
            ->assertRedirect(route('orders.show', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
            'cancellation_reason' => 'Changed my mind',
        ]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 2]);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'from_status' => 'pending',
            'to_status' => 'cancelled',
        ]);

        $order->refresh();
        $this->assertFalse($order->cancelAndRestoreInventory($user->id));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 2]);
    }

    public function test_checkout_rejects_quantity_above_available_stock(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Stock', 'slug' => 'stock']);
        $product = Product::create([
            'name' => 'Limited Product',
            'description' => 'Product used for stock tests',
            'price' => 50,
            'category_id' => $category->id,
            'stock_quantity' => 1,
            'status' => 'approved',
        ]);
        CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 2]);

        $this->actingAs($user)
            ->post(route('orders.store'), [
                'fulfilment_method' => 'delivery',
                'delivery_address' => 'Test address',
                'delivery_phone' => '1234567890',
            ])
            ->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 1]);
    }

    public function test_paystack_verification_confirms_order_idempotently(): void
    {
        config()->set('services.paystack.secret_key', 'test-secret');
        config()->set('services.paystack.base_url', 'https://api.paystack.co');
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Payments', 'slug' => 'payments']);
        $product = Product::create([
            'name' => 'Payment Product',
            'description' => 'Product used for payment tests',
            'price' => 50,
            'category_id' => $category->id,
            'stock_quantity' => 1,
            'status' => 'approved',
        ]);
        CartItem::create(['user_id' => $user->id, 'product_id' => $product->id, 'quantity' => 1]);

        Http::fake([
            '*/transaction/initialize' => Http::response([
                'status' => true,
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.test/authorize',
                    'access_code' => 'access-test',
                ],
            ]),
        ]);

        $this->actingAs($user)
            ->post(route('orders.store'), [
                'fulfilment_method' => 'delivery',
                'delivery_address' => 'Test address',
                'delivery_phone' => '1234567890',
            ])
            ->assertRedirect('https://checkout.paystack.test/authorize');

        $payment = PaymentTransaction::firstOrFail();
        $this->assertDatabaseHas('orders', ['id' => $payment->order_id, 'status' => 'pending']);

        $payload = [
            'status' => true,
            'data' => [
                'status' => 'success',
                'amount' => 5000,
                'reference' => $payment->reference,
            ],
        ];
        Http::fake(['*/transaction/verify/*' => Http::response($payload)]);

        $this->actingAs($user)
            ->get(route('payments.paystack.callback', ['reference' => $payment->reference]))
            ->assertRedirect(route('orders.show', $payment->order_id));

        $this->assertDatabaseHas('orders', ['id' => $payment->order_id, 'status' => 'confirmed']);
        $this->assertDatabaseHas('payment_transactions', ['id' => $payment->id, 'status' => 'paid']);
        $this->assertDatabaseCount('order_status_histories', 2);

        $this->actingAs($user)
            ->get(route('payments.paystack.callback', ['reference' => $payment->reference]))
            ->assertRedirect(route('orders.show', $payment->order_id));

        $this->assertDatabaseCount('order_status_histories', 2);
    }
}
