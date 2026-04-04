<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\DiscountCampaign;
use App\Models\HeroSlide;
use App\Models\Product;
use App\Models\User;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CatalogOrganizationFlowsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'super-admin']);
    }

    public function test_product_catalog_filters_by_category_price_and_stock(): void
    {
        $categoryA = Category::create([
            'name' => 'Gaming',
            'slug' => 'gaming',
        ]);

        $categoryB = Category::create([
            'name' => 'Accessories',
            'slug' => 'accessories',
        ]);

        Product::create([
            'name' => 'Alpha GPU',
            'description' => 'Great graphics card',
            'price' => 80,
            'category_id' => $categoryA->id,
            'stock_quantity' => 5,
            'status' => 'approved',
        ]);

        Product::create([
            'name' => 'Bravo GPU',
            'description' => 'Out of stock card',
            'price' => 220,
            'category_id' => $categoryA->id,
            'stock_quantity' => 0,
            'status' => 'approved',
        ]);

        Product::create([
            'name' => 'Cable Pack',
            'description' => 'Accessory item',
            'price' => 60,
            'category_id' => $categoryB->id,
            'stock_quantity' => 15,
            'status' => 'approved',
        ]);

        $response = $this->get(route('products.index', [
            'category' => 'gaming',
            'min_price' => 50,
            'max_price' => 100,
            'availability' => 'in_stock',
        ]));

        $response->assertOk();
        $response->assertSee('Alpha GPU');
        $response->assertDontSee('Bravo GPU');
        $response->assertDontSee('Cable Pack');
    }

    public function test_user_can_save_move_and_remove_wishlist_items(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $category = Category::create([
            'name' => 'Storage',
            'slug' => 'storage',
        ]);

        $product = Product::create([
            'name' => 'Compact SSD',
            'description' => 'Fast storage drive',
            'price' => 120,
            'category_id' => $category->id,
            'stock_quantity' => 8,
            'status' => 'approved',
        ]);

        $this->actingAs($user)
            ->post(route('wishlist.store', $product))
            ->assertSessionHas('success');

        $wishlistItem = WishlistItem::query()
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->firstOrFail();

        $this->actingAs($user)
            ->post(route('wishlist.move-to-cart', $wishlistItem))
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->assertDatabaseMissing('wishlist_items', [
            'id' => $wishlistItem->id,
        ]);

        $this->actingAs($user)
            ->post(route('wishlist.store', $product))
            ->assertSessionHas('success');

        $wishlistItem = WishlistItem::query()
            ->where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->firstOrFail();

        $this->actingAs($user)
            ->delete(route('wishlist.destroy', $wishlistItem))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('wishlist_items', [
            'id' => $wishlistItem->id,
        ]);
    }

    public function test_superadmin_can_restore_soft_deleted_campaign_and_slide(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $campaign = DiscountCampaign::create([
            'name' => 'Restore Promo',
            'code' => 'RESTORE20',
            'discount_percent' => 20,
            'is_active' => true,
        ]);

        $slide = HeroSlide::create([
            'title' => 'Restore Slide',
            'caption' => 'Promo visual',
            'image_url' => 'https://example.com/slide.jpg',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $campaign->delete();
        $slide->delete();

        $this->assertSoftDeleted('discount_campaigns', ['id' => $campaign->id]);
        $this->assertSoftDeleted('hero_slides', ['id' => $slide->id]);

        $this->actingAs($superAdmin)
            ->post(route('superadmin.marketing.campaigns.restore', $campaign->id))
            ->assertRedirect(route('superadmin.marketing.index'));

        $this->actingAs($superAdmin)
            ->post(route('superadmin.marketing.slides.restore', $slide->id))
            ->assertRedirect(route('superadmin.marketing.index'));

        $this->assertDatabaseHas('discount_campaigns', [
            'id' => $campaign->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('hero_slides', [
            'id' => $slide->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $superAdmin->id,
            'action' => 'superadmin.campaign.restored',
            'entity_type' => DiscountCampaign::class,
            'entity_id' => $campaign->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $superAdmin->id,
            'action' => 'superadmin.slide.restored',
            'entity_type' => HeroSlide::class,
            'entity_id' => $slide->id,
        ]);
    }
}
