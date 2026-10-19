<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Console Schedule
|--------------------------------------------------------------------------
|
| Automated daily schedule for rent invoice generation, daily reminders
| to unpaid tenants, and automatic 10% late penalty calculation after the 5th.
|
*/
Schedule::command('rent:process-reminders')
    ->dailyAt('08:00');
