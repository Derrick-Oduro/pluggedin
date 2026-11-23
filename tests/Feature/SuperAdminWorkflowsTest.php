<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Product;
use App\Models\ProductReport;
use App\Models\Shop;
use App\Models\TradeInRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SuperAdminWorkflowsTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $member;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['user', 'admin', 'super-admin'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
        // Fixed emails so searching "Ama" can't match a random fake address.
        $this->owner = User::factory()->create(['name' => 'Owner One', 'email' => 'owner@example.test']);
        $this->owner->assignRole('super-admin');
        $this->member = User::factory()->create(['name' => 'Ama Member', 'email' => 'ama@example.test']);
        $this->member->assignRole('user');
    }

    private function listing(array $extra = []): Product
    {
        $category = Category::firstOrCreate(['slug' => 'laptops'], ['name' => 'Laptops']);

        return Product::create($extra + ['name' => 'ThinkPad T480', 'description' => 'x', 'price' => 2500, 'category_id' => $category->id, 'stock_quantity' => 1, 'status' => 'approved', 'uploaded_by' => $this->member->id, 'is_user_uploaded' => true]);
    }

    public function test_sidebar_links_every_page_and_dashboard_shows_the_queue(): void
    {
        ContactMessage::create(['name' => 'Kofi', 'email' => 'k@example.com', 'subject' => 'Hi', 'message' => 'Question']);
        $tradeIn = TradeInRequest::create(['user_id' => $this->member->id, 'device_type' => 'Laptop', 'brand' => 'HP', 'model' => 'EliteBook', 'condition' => 'good', 'status' => 'submitted']);

        $page = $this->actingAs($this->owner)->get(route('superadmin.dashboard'))->assertOk();
        foreach (['superadmin.product-reports.index', 'superadmin.sellers.index', 'superadmin.settings.index', 'superadmin.audit-logs.index', 'superadmin.shops.index'] as $route) {
            $page->assertSee(route($route), false);
        }
        $page->assertSee('Waiting for you')->assertSee('Trade-ins to quote')->assertSee('Unanswered messages');
    }

    public function test_trade_in_goes_from_quote_to_completed_and_customer_is_notified(): void
    {
        $tradeIn = TradeInRequest::create(['user_id' => $this->member->id, 'device_type' => 'Laptop', 'brand' => 'HP', 'model' => 'EliteBook', 'condition' => 'good', 'status' => 'submitted']);

        $this->actingAs($this->owner)->get(route('superadmin.trade-ins.index'))->assertOk()->assertSee('Send offer');

        $this->actingAs($this->owner)->patch(route('superadmin.trade-ins.quote', $tradeIn), ['quoted_value' => 1800, 'admin_notes' => 'After battery check'])->assertSessionHas('success');
        $this->assertSame('quoted', $tradeIn->fresh()->status);
        $this->assertSame('Your trade-in has a quote', $this->member->notifications()->first()->data['title']);

        // Can't complete before the customer accepts.
        $this->actingAs($this->owner)->patch(route('superadmin.trade-ins.complete', $tradeIn))->assertStatus(422);

        $tradeIn->update(['status' => 'accepted']);
        $this->actingAs($this->owner)->patch(route('superadmin.trade-ins.complete', $tradeIn))->assertSessionHas('success');
        $this->assertSame('completed', $tradeIn->fresh()->status);
        $this->assertSame(2, $this->member->notifications()->count());
        $this->assertTrue(AuditLog::where('action', 'superadmin.trade_in.completed')->exists());
    }

    public function test_taking_down_a_reported_listing_hides_it_closes_reports_and_tells_the_seller(): void
    {
        $product = $this->listing();
        $reporters = User::factory()->count(2)->create();
        foreach ($reporters as $reporter) {
            ProductReport::create(['product_id' => $product->id, 'reporter_id' => $reporter->id, 'reason' => 'suspicious_listing']);
        }
        $report = ProductReport::first();

        $this->actingAs($this->owner)->get(route('superadmin.product-reports.index'))
            ->assertOk()->assertSee('Looks like a scam')->assertSee('1 other open report');

        $this->actingAs($this->owner)->patch(route('superadmin.product-reports.update', $report), ['action' => 'takedown', 'note' => 'Price too good to be true'])
            ->assertSessionHas('success');

        $this->assertSame('rejected', $product->fresh()->status);
        $this->assertSame('Price too good to be true', $product->fresh()->admin_review_comment);
        $this->assertSame(0, ProductReport::where('status', 'open')->count());
        $this->assertStringContainsString('Price too good to be true', $this->member->notifications()->first()->data['message']);
    }

    public function test_approving_a_shop_notifies_the_owner(): void
    {
        $shop = Shop::create(['owner_id' => $this->member->id, 'name' => 'Ama Gadgets', 'slug' => 'ama-gadgets', 'description' => 'Used laptops on campus', 'plan' => 'starter', 'status' => 'pending']);

        $this->actingAs($this->owner)->get(route('superadmin.shops.index'))->assertOk()->assertSee('Ama Gadgets')->assertSee('Approve');
        $this->actingAs($this->owner)->patch(route('superadmin.shops.update', $shop), ['status' => 'approved', 'plan' => 'starter'])->assertSessionHas('success');

        $this->assertSame('approved', $shop->fresh()->status);
        $this->assertSame('Your shop is approved', $this->member->notifications()->first()->data['title']);
    }

    public function test_messages_can_be_marked_handled_and_reopened(): void
    {
        $message = ContactMessage::create(['name' => 'Kofi', 'email' => 'k@example.com', 'subject' => 'Delivery to Legon?', 'message' => 'Do you deliver?']);

        $this->actingAs($this->owner)->get(route('superadmin.contact-messages.index'))->assertSee('Delivery to Legon?')->assertSee('Reply by email');
        $this->actingAs($this->owner)->patch(route('superadmin.contact-messages.update', $message), ['handled' => 1]);
        $this->assertNotNull($message->fresh()->handled_at);
        $this->actingAs($this->owner)->get(route('superadmin.contact-messages.index'))->assertDontSee('Delivery to Legon?');

        $this->actingAs($this->owner)->patch(route('superadmin.contact-messages.update', $message), ['handled' => 0]);
        $this->assertNull($message->fresh()->handled_at);
    }

    public function test_the_last_super_admin_cannot_lose_access(): void
    {
        $this->actingAs($this->owner)->put(route('superadmin.users.update', $this->owner), ['name' => 'Owner One', 'email' => $this->owner->email, 'role' => 'admin'])
            ->assertSessionHas('error');
        $this->assertTrue($this->owner->fresh()->hasRole('super-admin'));

        $second = User::factory()->create();
        $second->assignRole('super-admin');
        // Still can't demote yourself, even with another super admin around.
        $this->actingAs($this->owner)->put(route('superadmin.users.update', $this->owner), ['name' => 'Owner One', 'email' => $this->owner->email, 'role' => 'user'])
            ->assertSessionHas('error');
        // But another super admin can be demoted while one remains.
        $this->actingAs($this->owner)->put(route('superadmin.users.update', $second), ['name' => $second->name, 'email' => $second->email, 'role' => 'admin'])
            ->assertSessionHas('success');
        $this->assertTrue($second->fresh()->hasRole('admin'));
    }

    public function test_users_and_sellers_can_be_searched(): void
    {
        $this->listing();

        $this->actingAs($this->owner)->get(route('superadmin.users.index', ['q' => 'Ama']))->assertSee('Ama Member')->assertDontSee('Owner One</p>', false);
        $this->actingAs($this->owner)->get(route('superadmin.sellers.index', ['q' => 'Ama']))->assertSee('Ama Member');

        $this->actingAs($this->owner)->patch(route('superadmin.sellers.verification', $this->member))->assertSessionHas('success');
        $this->assertNotNull($this->member->fresh()->seller_verified_at);
        $this->actingAs($this->owner)->get(route('superadmin.audit-logs.index', ['area' => 'seller']))->assertSee('verified seller');
    }
}
