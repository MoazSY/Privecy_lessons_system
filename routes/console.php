<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('zoom:sessions:auto-create')->everyMinute()->appendOutputTo(storage_path('logs/zoom-auto-create.log'));
Schedule::command('zoom:sessions:auto-end')->everyMinute()->appendOutputTo(storage_path('logs/zoom-auto-end.log'));


