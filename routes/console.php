<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send birthday emails every morning at 6:00 AM
Schedule::command('birthday:send')->dailyAt('06:00');

// On the 1st of every month at 07:00, email inactive/irregular attendance report for the previous month
Schedule::command('attendance:monthly-report')->monthlyOn(1, '07:00');
