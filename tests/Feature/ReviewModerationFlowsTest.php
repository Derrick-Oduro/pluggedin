<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReviewModerationFlowsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'super-admin']);
    }

    public function test_review_submission_defaults_to_pending_moderation(): void
    {
        $buyer = User::factory()->create();
        $buyer->assignRole('user');

        $category = Category::create(['name' => 'Review Category', 'slug' => 'review-category']);

        $product = Product::create([
            'name' => 'Review Product',
            'description' => 'Review description',
            'price' => 99,
            'category_id' => $category->id,
            'stock_quantity' => 5,
            'status' => 'approved',
        ]);

        $order = Order::create([
            'user_id' => $buyer->id,
            'total_price' => 99,
            'status' => 'completed',
            'delivery_address' => 'Address',
            'delivery_phone' => '1234567890',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 99,
        ]);

        $this->actingAs($buyer)->post(route('products.reviews.store', $product), [
            'rating' => 5,
            'comment' => 'Great',
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $product->id,
            'user_id' => $buyer->id,
            'moderation_status' => 'pending',
        ]);
    }

    public function test_hidden_reviews_are_not_visible_on_product_page(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('user');

        $reviewer = User::factory()->create();
        $reviewer->assignRole('user');

        $category = Category::create(['name' => 'Visibility Category', 'slug' => 'visibility-category']);

        $product = Product::create([
            'name' => 'Visibility Product',
            'description' => 'Visibility description',
            'price' => 70,
            'category_id' => $category->id,
            'stock_quantity' => 8,
            'status' => 'approved',
        ]);

        ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $reviewer->id,
            'order_id' => null,
            'rating' => 2,
            'comment' => 'Should not be visible',
            'moderation_status' => 'hidden',
        ]);

        $response = $this->actingAs($owner)->get(route('products.show', $product));
        $response->assertOk();
        $response->assertDontSee('Should not be visible');
    }

    public function test_user_can_report_other_user_review(): void
    {
        $reporter = User::factory()->create();
        $reporter->assignRole('user');

        $author = User::factory()->create();
        $author->assignRole('user');

        $category = Category::create(['name' => 'Report Category', 'slug' => 'report-category']);

        $product = Product::create([
            'name' => 'Report Product',
            'description' => 'Report description',
            'price' => 42,
            'category_id' => $category->id,
            'stock_quantity' => 8,
            'status' => 'approved',
        ]);

        $review = ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $author->id,
            'order_id' => null,
            'rating' => 1,
            'comment' => 'Bad',
            'moderation_status' => 'approved',
        ]);

        $this->actingAs($reporter)->post(route('reviews.report', $review), [
            'reason' => 'Abusive language',
        ])->assertSessionHas('success');

        $review->refresh();

        $this->assertTrue($review->is_reported);
        $this->assertEquals('Abusive language', $review->report_reason);
    }
}
