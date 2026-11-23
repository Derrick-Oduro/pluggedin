<?php

namespace Tests\Feature;

use App\Models\DiscountCampaign;
use App\Models\HeroSlide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HomepageAndDiscountsTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'super-admin']);
        $this->owner = User::factory()->create();
        $this->owner->assignRole('super-admin');
    }

    private function campaign(array $extra = []): DiscountCampaign
    {
        return DiscountCampaign::create($extra + ['name' => 'Back to school', 'code' => 'BACK2SCHOOL', 'discount_percent' => 10, 'is_active' => true]);
    }

    public function test_campaign_states_follow_dates_limits_and_switch(): void
    {
        $this->assertSame('live', $this->campaign()->state());
        $this->assertSame('paused', $this->campaign(['code' => 'A', 'is_active' => false])->state());
        $this->assertSame('scheduled', $this->campaign(['code' => 'B', 'starts_at' => now()->addDay()])->state());
        $this->assertSame('ended', $this->campaign(['code' => 'C', 'ends_at' => now()->subMinute()])->state());
        $this->assertSame('used_up', $this->campaign(['code' => 'D', 'max_uses' => 5, 'used_count' => 5])->state());
    }

    public function test_campaign_can_be_paused_resumed_and_ended(): void
    {
        $campaign = $this->campaign(['ends_at' => now()->addWeek()]);

        $this->actingAs($this->owner)->patch(route('superadmin.marketing.campaigns.state', $campaign), ['action' => 'pause'])->assertSessionHas('success');
        $this->assertSame('paused', $campaign->fresh()->state());
        $this->assertSame(0, DiscountCampaign::active()->count());

        $this->actingAs($this->owner)->patch(route('superadmin.marketing.campaigns.state', $campaign), ['action' => 'resume']);
        $this->assertSame('live', $campaign->fresh()->state());

        $this->actingAs($this->owner)->patch(route('superadmin.marketing.campaigns.state', $campaign), ['action' => 'end']);
        $this->assertSame('ended', $campaign->fresh()->state());

        // A finished campaign can't simply be resumed; it needs a new end date first.
        $this->actingAs($this->owner)->patch(route('superadmin.marketing.campaigns.state', $campaign), ['action' => 'resume'])->assertSessionHas('error');
        $this->assertSame('ended', $campaign->fresh()->state());
    }

    public function test_saving_a_campaign_keeps_its_dates_and_limit(): void
    {
        $campaign = $this->campaign(['max_uses' => 50, 'starts_at' => now()->subDay()->setTime(8, 0), 'ends_at' => now()->addWeek()->setTime(20, 0)]);

        // The edit form sends everything back, including dates and the limit.
        $this->actingAs($this->owner)->patch(route('superadmin.marketing.campaigns.update', $campaign), [
            'name' => 'Back to school 2',
            'code' => 'back2school',
            'discount_percent' => 15,
            'max_uses' => 50,
            'starts_date' => $campaign->starts_at->toDateString(), 'starts_time' => '08:00',
            'ends_date' => $campaign->ends_at->toDateString(), 'ends_time' => '20:00',
            'is_active' => 1,
        ])->assertSessionHas('success');

        $fresh = $campaign->fresh();
        $this->assertSame('Back to school 2', $fresh->name);
        $this->assertSame(15, (int) $fresh->discount_percent);
        $this->assertSame(50, $fresh->max_uses);
        $this->assertSame('20:00', $fresh->ends_at->format('H:i'));
        $this->assertSame('BACK2SCHOOL', $fresh->code);
    }

    public function test_codes_must_be_unique_and_clean(): void
    {
        $this->campaign();

        $this->actingAs($this->owner)->post(route('superadmin.marketing.campaigns.store'), ['name' => 'Dupe', 'code' => 'back2school', 'discount_percent' => 5])
            ->assertSessionHasErrors('code');
        $this->actingAs($this->owner)->post(route('superadmin.marketing.campaigns.store'), ['name' => 'Bad', 'code' => 'HAS SPACE', 'discount_percent' => 5])
            ->assertSessionHasErrors('code');
        $this->assertSame(1, DiscountCampaign::count());
    }

    public function test_duplicate_archive_and_restore(): void
    {
        $campaign = $this->campaign(['used_count' => 3]);

        $this->actingAs($this->owner)->post(route('superadmin.marketing.campaigns.duplicate', $campaign));
        $copy = DiscountCampaign::where('name', 'Back to school (copy)')->firstOrFail();
        $this->assertSame('BACK2SCHOOL-2', $copy->code);
        $this->assertSame(0, $copy->used_count);
        $this->assertSame('paused', $copy->state());

        $this->actingAs($this->owner)->delete(route('superadmin.marketing.campaigns.destroy', $campaign));
        $this->assertSoftDeleted($campaign);
        $this->actingAs($this->owner)->get(route('superadmin.marketing.index', ['section' => 'discounts', 'tab' => 'archived']))->assertSee('Restore');

        $this->actingAs($this->owner)->post(route('superadmin.marketing.campaigns.restore', $campaign->id));
        $this->assertSame('paused', $campaign->fresh()->state(), 'Restored campaigns come back paused');
    }

    public function test_page_shows_what_shoppers_get_and_warns_about_overlapping_automatic_discounts(): void
    {
        $this->campaign(['name' => 'Freshers', 'code' => null, 'discount_percent' => 10]);
        $this->campaign(['name' => 'Flash sale', 'code' => null, 'discount_percent' => 20]);

        $this->actingAs($this->owner)->get(route('superadmin.marketing.index'))
            ->assertOk()
            ->assertSee('Shoppers get 20% off every order right now')
            ->assertSee('2 automatic discounts are live.')
            ->assertSee('Pause');
    }

    public function test_homepage_image_can_be_put_on_and_taken_off(): void
    {
        $first = HeroSlide::create(['title' => 'Desk', 'image_url' => 'https://example.com/a.jpg', 'is_active' => true]);
        $second = HeroSlide::create(['title' => 'Phone', 'image_url' => 'https://example.com/b.jpg', 'is_active' => false]);

        $this->actingAs($this->owner)->get(route('superadmin.marketing.index', ['section' => 'homepage']))->assertSee('On the homepage now')->assertSee('Put on homepage');

        $this->actingAs($this->owner)->post(route('superadmin.marketing.slides.make-live', $second));
        $this->assertTrue($second->fresh()->is_active);
        $this->assertFalse($first->fresh()->is_active);

        $this->actingAs($this->owner)->post(route('superadmin.marketing.slides.take-off', $second));
        $this->assertSame(0, HeroSlide::active()->count());

        // Editing details without a new photo keeps the current image.
        $this->actingAs($this->owner)->patch(route('superadmin.marketing.slides.update', $first), ['title' => 'Desk 2', 'caption' => 'Tested on campus']);
        $this->assertSame('https://example.com/a.jpg', $first->fresh()->image_url);
        $this->assertSame('Tested on campus', $first->fresh()->caption);
    }
}
