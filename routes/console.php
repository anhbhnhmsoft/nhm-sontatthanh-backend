<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:refresh-access-token')->twiceDaily(1, 13);
Schedule::command('app:fresh-state-camera')->hourly();
Schedule::command('app:fresh-code-verify-sale')->everyTwoHours();
