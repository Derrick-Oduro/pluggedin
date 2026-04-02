<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\DiscountCampaign;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DiscountAndAuditFlowsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'user']);
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'super-admin']);
    }

    public function test_checkout_applies_promo_discount_and_tracks_usage(): void
    {
        $buyer = User::factory()->create();
        $buyer->assignRole('user');

        $category = Category::create([
            'name' => 'Discounted Hardware',
            'slug' => 'discounted-hardware',
        ]);

        $product = Product::create([
            'name' => 'Discount Test SSD',
            'description' => 'Storage device for discount test',
            'price' => 100,
            'category_id' => $category->id,
            'stock_quantity' => 10,
            'status' => 'approved',
        ]);

        $campaign = DiscountCampaign::create([
            'name' => 'Spring Promo',
            'code' => 'SPRING20',
            'discount_percent' => 20,
            'max_uses' => 5,
            'used_count' => 0,
            'is_active' => true,
        ]);

        CartItem::create([
            'user_id' => $buyer->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($buyer)->post(route('orders.store'), [
            'delivery_address' => '123 Promo Road',
            'delivery_phone' => '1234567890',
            'promo_code' => 'SPRING20',
        ]);

        $response->assertRedirect(route('orders.index'));

        $this->assertDatabaseHas('orders', [
            'user_id' => $buyer->id,
            'subtotal_price' => 200,
            'discount_amount' => 40,
            'total_price' => 160,
            'discount_campaign_id' => $campaign->id,
            'applied_discount_code' => 'SPRING20',
        ]);

        $campaign->refresh();
        $this->assertEquals(1, $campaign->used_count);
    }

    public function test_superadmin_campaign_changes_are_audited(): void
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $response = $this->actingAs($superAdmin)->post(route('superadmin.marketing.campaigns.store'), [
            'name' => 'Audit Promo',
            'code' => 'AUDIT10',
            'discount_percent' => 10,
            'max_uses' => 100,
            'starts_date' => now()->toDateString(),
            'starts_time' => '08:00',
            'ends_date' => now()->addDays(5)->toDateString(),
            'ends_time' => '20:00',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('superadmin.marketing.index'));

        $campaign = DiscountCampaign::where('code', 'AUDIT10')->firstOrFail();

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $superAdmin->id,
            'action' => 'superadmin.campaign.created',
            'entity_type' => DiscountCampaign::class,
            'entity_id' => $campaign->id,
        ]);
    }
}
