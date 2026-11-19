<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Renders every main page so a broken Blade template fails the suite
 * instead of showing up as a 500 in the browser.
 */
class PageRenderSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['user', 'admin', 'super-admin'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
        Category::create(['name' => 'Laptops', 'slug' => 'laptops']);
    }

    public function test_guest_pages_render(): void
    {
        foreach (['/', '/products', '/services', '/about', '/contact', '/terms', '/privacy', '/login', '/register', '/forgot-password'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_customer_pages_render(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        foreach (['/cart', '/orders', '/bookings', '/wishlist', '/my-uploads', '/products/upload', '/my-reviews', '/profile', '/search?q=ssd', '/my-trade-ins'] as $url) {
            $this->actingAs($user)->get($url)->assertOk();
        }
    }

    public function test_admin_pages_render(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        foreach (['/admin/dashboard', '/admin/products', '/admin/products/create', '/admin/products/pending', '/admin/orders', '/admin/bookings', '/admin/reviews', '/admin/referrals'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_super_admin_pages_render(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('super-admin');

        foreach (['/superadmin/dashboard', '/superadmin/users','/superadmin/categories', '/superadmin/settings', '/superadmin/audit-logs', '/superadmin/marketing', '/superadmin/contact-messages', '/superadmin/trade-ins', '/superadmin/shops', '/superadmin/product-reports', '/superadmin/sellers'] as $url) {
            $this->actingAs($owner)->get($url)->assertOk();
        }
    }
}
