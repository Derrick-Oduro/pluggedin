<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Release stock held by orders whose payment was never completed.
\Illuminate\Support\Facades\Schedule::command('orders:expire-unpaid --minutes=60')
    ->everyTenMinutes()
    ->withoutOverlapping();
