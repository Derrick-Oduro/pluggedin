<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LaunchSafetyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['user', 'admin', 'super-admin'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
        config(['services.paystack.secret_key' => 'sk_test_x']);
    }

    private function pendingOrder(int $minutesAgo, int $qty = 2): array
    {
        $category = Category::firstOrCreate(['slug' => 'memory'], ['name' => 'Memory']);
        $product = Product::create(['name' => 'RAM', 'description' => 'x', 'price' => 50, 'category_id' => $category->id, 'stock_quantity' => 0, 'status' => 'approved']);
        $order = Order::create(['user_id' => User::factory()->create()->id, 'total_price' => 100, 'status' => 'pending', 'fulfilment_method' => 'delivery', 'delivery_address' => 'Hall', 'delivery_phone' => '0200000000']);
        OrderItem::create(['order_id' => $order->id, 'product_id' => $product->id, 'quantity' => $qty, 'price' => 50]);
        $payment = PaymentTransaction::create(['order_id' => $order->id, 'reference' => 'PLUG-'.$order->id, 'status' => 'pending', 'amount' => 100, 'currency' => 'GHS']);
        Order::whereKey($order->id)->update(['created_at' => now()->subMinutes($minutesAgo)]);

        return [$order, $product, $payment];
    }

    public function test_abandoned_order_is_cancelled_and_stock_returned(): void
    {
        [$order, $product] = $this->pendingOrder(90);
        Http::fake(['*/transaction/verify/*' => Http::response(['status' => true, 'data' => ['status' => 'abandoned', 'amount' => 10000]])]);

        $this->artisan('orders:expire-unpaid')->assertSuccessful();

        $this->assertSame('cancelled', $order->fresh()->status);
        $this->assertSame(2, $product->fresh()->stock_quantity);
    }

    public function test_late_payment_is_confirmed_not_cancelled(): void
    {
        [$order, $product, $payment] = $this->pendingOrder(90);
        Http::fake(['*/transaction/verify/*' => Http::response(['status' => true, 'data' => ['status' => 'success', 'amount' => 10000]])]);

        $this->artisan('orders:expire-unpaid')->assertSuccessful();

        $this->assertSame('confirmed', $order->fresh()->status);
        $this->assertSame('paid', $payment->fresh()->status);
        $this->assertSame(0, $product->fresh()->stock_quantity);
    }

    public function test_orders_are_left_alone_when_paystack_is_unreachable_or_recent(): void
    {
        [$old] = $this->pendingOrder(90);
        [$recent] = $this->pendingOrder(5);
        Http::fake(['*/transaction/verify/*' => Http::response('down', 500)]);

        $this->artisan('orders:expire-unpaid')->assertSuccessful();

        $this->assertSame('pending', $old->fresh()->status);
        $this->assertSame('pending', $recent->fresh()->status);
    }

    public function test_customer_cannot_self_cancel_a_paid_order(): void
    {
        [$order] = $this->pendingOrder(1);
        $order->update(['status' => 'confirmed']);

        $this->actingAs($order->user)->patch(route('orders.cancel', $order))->assertStatus(422);
        $this->assertSame('confirmed', $order->fresh()->status);
    }

    public function test_seeding_in_production_creates_no_default_accounts_and_resets_nothing(): void
    {
        $this->app['env'] = 'production';
        $existing = User::factory()->create(['email' => 'superadmin@pluggedin.com', 'password' => Hash::make('a-strong-secret-123')]);

        // Same command docker/start.sh runs; must be safe to run on every boot.
        $this->artisan('db:seed', ['--force' => true])->assertSuccessful();
        $this->artisan('db:seed', ['--force' => true])->assertSuccessful();

        $this->assertTrue(Hash::check('a-strong-secret-123', $existing->fresh()->password));
        $this->assertDatabaseMissing('users', ['email' => 'admin@pluggedin.com']);
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
        $this->assertSame(0, Product::count());
        $this->assertDatabaseHas('categories', ['slug' => 'laptops']);
    }

    public function test_uploaded_photos_are_resized_and_converted(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $path = \App\Support\ImageOptimizer::store(\Illuminate\Http\UploadedFile::fake()->image('big.jpg', 3200, 2400), 'products');

        $this->assertStringEndsWith('.webp', $path);
        [$width, $height] = getimagesizefromstring(\Illuminate\Support\Facades\Storage::disk('public')->get($path));
        $this->assertSame([1600, 1200], [$width, $height]);
    }

    public function test_site_settings_save_in_one_go_and_show_in_footer(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('super-admin');

        $this->actingAs($owner)->patch(route('superadmin.settings.update'), [
            'site_title' => 'PluggedIn',
            'contact_email' => 'hello@pluggedin.com.gh',
            'contact_phone' => '+233 55 123 4567',
            'contact_address' => 'Legon, Accra',
        ])->assertRedirect(route('superadmin.settings.index'));

        $this->get('/about')->assertSee('hello@pluggedin.com.gh')->assertSee('+233 55 123 4567')->assertSee('Legon, Accra')
            ->assertDontSee('000 0000');
    }

    public function test_social_links_from_settings_show_in_footer(): void
    {
        $owner = User::factory()->create();
        $owner->assignRole('super-admin');

        $this->actingAs($owner)->patch(route('superadmin.settings.update'), [
            'site_title' => 'PluggedIn',
            'contact_email' => 'hello@pluggedin.com.gh',
            'contact_phone' => '0594042521',
            'instagram_url' => 'https://instagram.com/pluggedin',
        ])->assertSessionHasNoErrors();

        $this->get('/about')
            ->assertSee('https://wa.me/233594042521', false)
            ->assertSee('https://instagram.com/pluggedin', false)
            ->assertDontSee('aria-label="Facebook"', false)
            ->assertSee('Your One-Stop Student Hub');
    }

    public function test_checkout_is_rate_limited(): void
    {
        $user = User::factory()->create();
        for ($i = 0; $i < 6; $i++) {
            $this->actingAs($user)->post(route('orders.store'), [])->assertStatus(302);
        }
        $this->actingAs($user)->post(route('orders.store'), [])->assertStatus(429);
    }

    public function test_create_admin_command_sets_role_and_password(): void
    {
        $this->artisan('app:create-admin', ['email' => 'Owner@Example.com', '--super' => true])
            ->expectsQuestion('Password (min 12 characters, input hidden)', 'correct-horse-42')
            ->expectsQuestion('Confirm password', 'correct-horse-42')
            ->assertSuccessful();

        $user = User::where('email', 'owner@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('super-admin'));
        $this->assertTrue(Hash::check('correct-horse-42', $user->password));
    }
}
