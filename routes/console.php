<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('transactions:process-recurring')->daily();

// Email notifications
Schedule::command('notifications:monthly-digest')->monthlyOn(1, '08:00'); // 1st of each month at 8 AM
Schedule::command('notifications:low-balance')->dailyAt('09:00'); // Every day at 9 AM
