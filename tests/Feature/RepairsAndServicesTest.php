<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RepairsAndServicesTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['user', 'super-admin'] as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
        $this->owner = User::factory()->create();
        $this->owner->assignRole('super-admin');
        $this->customer = User::factory()->create();
        $this->customer->assignRole('user');
        \App\Models\Category::create(['name' => 'Memory', 'slug' => 'memory']);
        \App\Models\Category::create(['name' => 'Storage', 'slug' => 'storage']);
        $this->seed(ServiceSeeder::class);
    }

    public function test_repairs_page_groups_services_and_hides_hidden_ones(): void
    {
        Service::where('name', 'Keyboard replacement')->update(['is_active' => false]);

        $this->get(route('services.index'))
            ->assertOk()
            ->assertSeeInOrder(['Not sure what’s wrong?', 'Device check-up', 'Repairs', 'Battery replacement', 'Upgrades', 'SSD Upgrade'])
            ->assertSee('Shop parts')
            ->assertDontSee('Keyboard replacement');

        $hidden = Service::where('name', 'Keyboard replacement')->first();
        $this->get(route('services.show', $hidden))->assertNotFound();
        $this->actingAs($this->customer)->get(route('bookings.create', $hidden))->assertNotFound();
    }

    public function test_super_admin_can_add_edit_hide_reorder_and_delete_services(): void
    {
        $this->actingAs($this->owner)->get(route('superadmin.services.index'))->assertOk()->assertSee('Repairs &amp; services', false)->assertSee('Battery replacement');

        $this->actingAs($this->owner)->post(route('superadmin.services.store'), [
            'name' => 'Speaker repair', 'type' => 'repair', 'description' => 'Crackly or silent speaker fixed.',
            'price' => 120, 'price_is_from' => 1, 'turnaround' => 'Same day', 'device_types' => ['phone'], 'is_active' => 1,
        ])->assertSessionHas('success');
        $service = Service::where('name', 'Speaker repair')->firstOrFail();
        $this->assertSame('From GH₵120.00', $service->priceLabel());
        $this->assertSame(['phone'], $service->device_types);

        $this->actingAs($this->owner)->patch(route('superadmin.services.update', $service), [
            'name' => 'Speaker & mic repair', 'type' => 'repair', 'description' => 'Fixed.', 'price' => 150, 'is_active' => 1,
        ])->assertSessionHas('success');
        $this->assertSame('GH₵150.00', $service->fresh()->priceLabel());

        $this->actingAs($this->owner)->patch(route('superadmin.services.toggle', $service));
        $this->assertFalse($service->fresh()->is_active);

        $before = Service::where('type', 'repair')->ordered()->pluck('id')->all();
        $this->actingAs($this->owner)->patch(route('superadmin.services.move', $before[1]), ['direction' => 'up']);
        $after = Service::where('type', 'repair')->ordered()->pluck('id')->all();
        $this->assertSame([$before[1], $before[0]], array_slice($after, 0, 2));

        $this->actingAs($this->owner)->delete(route('superadmin.services.destroy', $service))->assertSessionHas('success');
        $this->assertModelMissing($service);
    }

    public function test_services_with_bookings_cannot_be_deleted(): void
    {
        $service = Service::where('name', 'Battery replacement')->first();
        Booking::create(['user_id' => $this->customer->id, 'service_id' => $service->id, 'device_model' => 'iPhone 11', 'preferred_date' => now()->addDays(2), 'status' => 'pending']);

        $this->actingAs($this->owner)->delete(route('superadmin.services.destroy', $service))->assertSessionHas('error');
        $this->assertModelExists($service);
    }

    public function test_booking_collects_device_phone_and_handover(): void
    {
        $service = Service::where('name', 'Phone screen replacement')->first();

        $this->actingAs($this->customer)->get(route('bookings.create', $service))->assertOk()->assertSee('Collect it from me')->assertSee('From GH₵350.00');

        // A laptop can't be booked for a phone-only service.
        $this->actingAs($this->customer)->post(route('bookings.store'), [
            'service_id' => $service->id, 'device_type' => 'laptop', 'device_model' => 'Dell', 'contact_phone' => '0241234567',
            'handover_method' => 'drop_off', 'preferred_date' => now()->addDays(2)->toDateString(),
        ])->assertSessionHasErrors('device_type');

        // Pickup needs an address.
        $this->actingAs($this->customer)->post(route('bookings.store'), [
            'service_id' => $service->id, 'device_type' => 'phone', 'device_model' => 'iPhone 11', 'contact_phone' => '0241234567',
            'handover_method' => 'pickup', 'preferred_date' => now()->addDays(2)->toDateString(),
        ])->assertSessionHasErrors('pickup_location');

        $this->actingAs($this->customer)->post(route('bookings.store'), [
            'service_id' => $service->id, 'device_type' => 'phone', 'device_model' => 'iPhone 11', 'contact_phone' => '024 123 4567',
            'handover_method' => 'pickup', 'pickup_location' => 'Commonwealth Hall, room 12', 'preferred_date' => now()->addDays(2)->toDateString(),
        ])->assertSessionHas('success');

        $booking = Booking::firstOrFail();
        $this->assertSame('phone', $booking->device_type);
        $this->assertSame('We collect it from Commonwealth Hall, room 12', $booking->handoverLabel());
        $this->actingAs($this->customer)->get(route('bookings.show', $booking))->assertSee('024 123 4567');
    }

    public function test_diagnosis_needs_a_description_of_the_problem(): void
    {
        $service = Service::where('type', 'diagnosis')->first();

        $this->actingAs($this->customer)->post(route('bookings.store'), [
            'service_id' => $service->id, 'device_type' => 'laptop', 'device_model' => 'HP 250', 'contact_phone' => '0241234567',
            'handover_method' => 'drop_off', 'preferred_date' => now()->addDays(2)->toDateString(),
        ])->assertSessionHasErrors('notes');
    }
}
