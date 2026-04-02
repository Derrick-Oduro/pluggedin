<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PointsTransaction;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\ReferralLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MarketplaceFlowsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'super-admin']);
    }

    public function test_user_upload_is_saved_as_pending(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $category = Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
        ]);

        $response = $this->actingAs($user)->post(route('products.upload.store'), [
            'name' => 'Student Laptop',
            'description' => 'Great condition and suitable for school.',
            'price' => 500,
            'category_id' => $category->id,
            'stock_quantity' => 3,
        ]);

        $response->assertRedirect(route('user.dashboard'));

        $this->assertDatabaseHas('products', [
            'name' => 'Student Laptop',
            'uploaded_by' => $user->id,
            'status' => 'pending',
            'is_user_uploaded' => true,
        ]);
    }

    public function test_monthly_upload_limit_is_enforced_for_user_role(): void
    {
        config()->set('marketplace.upload_limit_per_month', 1);

        $user = User::factory()->create();
        $user->assignRole('user');

        $category = Category::create([
            'name' => 'Accessories',
            'slug' => 'accessories',
        ]);

        Product::create([
            'name' => 'Existing Upload',
            'description' => 'Already uploaded this month',
            'price' => 50,
            'category_id' => $category->id,
            'stock_quantity' => 1,
            'uploaded_by' => $user->id,
            'is_user_uploaded' => true,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->post(route('products.upload.store'), [
            'name' => 'Blocked Upload',
            'description' => 'Should be blocked by limit',
            'price' => 100,
            'category_id' => $category->id,
            'stock_quantity' => 1,
        ]);

        $response->assertRedirect(route('products.upload.create'));
        $response->assertSessionHas('error');

        $this->assertDatabaseMissing('products', [
            'name' => 'Blocked Upload',
            'uploaded_by' => $user->id,
        ]);
    }

    public function test_only_verified_buyer_can_submit_review(): void
    {
        $buyer = User::factory()->create();
        $buyer->assignRole('user');

        $nonBuyer = User::factory()->create();
        $nonBuyer->assignRole('user');

        $category = Category::create([
            'name' => 'Components',
            'slug' => 'components',
        ]);

        $product = Product::create([
            'name' => 'RAM 16GB',
            'description' => 'High speed memory',
            'price' => 80,
            'category_id' => $category->id,
            'stock_quantity' => 10,
            'status' => 'approved',
        ]);

        $order = Order::create([
            'user_id' => $buyer->id,
            'total_price' => 80,
            'status' => 'completed',
            'delivery_address' => '123 Main St',
            'delivery_phone' => '0001112222',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 80,
        ]);

        $this->actingAs($buyer)->post(route('products.reviews.store', $product), [
            'rating' => 5,
            'comment' => 'Excellent product.',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $product->id,
            'user_id' => $buyer->id,
            'rating' => 5,
        ]);

        $this->actingAs($nonBuyer)->post(route('products.reviews.store', $product), [
            'rating' => 4,
            'comment' => 'Trying without purchase.',
        ])->assertSessionHas('error');

        $this->assertDatabaseMissing('product_reviews', [
            'product_id' => $product->id,
            'user_id' => $nonBuyer->id,
        ]);
    }

    public function test_referral_points_are_awarded_on_checkout_conversion(): void
    {
        config()->set('marketplace.referral_points_per_purchase', 10);

        $referrer = User::factory()->create(['points_balance' => 0]);
        $referrer->assignRole('user');

        $buyer = User::factory()->create();
        $buyer->assignRole('user');

        $category = Category::create([
            'name' => 'Storage',
            'slug' => 'storage',
        ]);

        $product = Product::create([
            'name' => 'SSD 1TB',
            'description' => 'Fast storage',
            'price' => 120,
            'category_id' => $category->id,
            'stock_quantity' => 20,
            'status' => 'approved',
        ]);

        $link = ReferralLink::create([
            'user_id' => $referrer->id,
            'product_id' => $product->id,
            'code' => 'TESTCODE12345678',
        ]);

        CartItem::create([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($buyer)
            ->withSession(['referrals' => [$product->id => $link->id]])
            ->post(route('orders.store'), [
                'delivery_address' => '12 Test Avenue',
                'delivery_phone' => '1234567890',
            ]);

        $response->assertRedirect(route('orders.index'));

        $referrer->refresh();
        $link->refresh();

        $this->assertEquals(10, $referrer->points_balance);
        $this->assertEquals(1, $link->conversions);

        $this->assertDatabaseHas('points_transactions', [
            'user_id' => $referrer->id,
            'type' => 'referral_purchase',
            'points' => 10,
        ]);

        $this->assertTrue(PointsTransaction::where('user_id', $referrer->id)->exists());
    }
}
