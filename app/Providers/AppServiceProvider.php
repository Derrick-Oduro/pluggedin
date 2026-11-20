<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Support\SiteSettings;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $this->configureRateLimits();

        // Footer, contact and legal pages read the details edited in super admin Settings.
        View::composer(['layouts.app', 'layouts.guest', 'home', 'contact', 'legal.*'], fn ($view) => $view->with('site', SiteSettings::all()));

        // Shop categories for the header menus: only ones with something on sale, cached briefly.
        View::composer(['layouts.navigation', 'components.mobile-menu'], fn ($view) => $view->with('navCategories', \Illuminate\Support\Facades\Cache::remember(
            'nav.categories', now()->addMinutes(10),
            fn () => \App\Models\Category::query()
                ->whereHas('products', fn ($q) => $q->approved())
                ->orderBy('name')
                ->get(['id', 'name', 'slug'])
        )));
    }

    /** Named limits used as throttle:<name> in routes. */
    private function configureRateLimits(): void
    {
        // Sign-up and password-reset forms (the reset form sends email).
        RateLimiter::for('auth-forms', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));

        // Each checkout reserves stock and opens a Paystack transaction.
        RateLimiter::for('checkout', fn (Request $request) => Limit::perMinute(6)->by($request->user()?->id ?: $request->ip()));

        // Listings, reviews, reports, bookings, trade-ins and shop applications.
        RateLimiter::for('submissions', fn (Request $request) => Limit::perMinute(10)->by($request->user()?->id ?: $request->ip()));
    }
}
