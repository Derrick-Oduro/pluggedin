<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Removes the demo listings added by DemoCatalogSeeder and their photos.
Artisan::command('catalog:demo-remove', function () {
    $dir = \Database\Seeders\DemoCatalogSeeder::IMAGE_DIR;
    $names = array_column(require database_path('seeders/data/demo_catalog.php'), 'name');
    $products = \App\Models\Product::whereIn('name', $names)->where('is_user_uploaded', false)->get();

    $products->each->delete(); // carts, wishlists, reviews and order lines cascade
    \Illuminate\Support\Facades\Storage::disk('public')->deleteDirectory($dir);

    $this->info("Removed {$products->count()} demo listings.");
})->purpose('Delete the demo catalogue listings and their photos');

// Release stock held by orders whose payment was never completed.
\Illuminate\Support\Facades\Schedule::command('orders:expire-unpaid --minutes=60')
    ->everyTenMinutes()
    ->withoutOverlapping();
