<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('zoom:sessions:auto-create')->everyMinute()->appendOutputTo(storage_path('logs/zoom-auto-create.log'));
Schedule::command('zoom:sessions:auto-end')->everyMinute()->appendOutputTo(storage_path('logs/zoom-auto-end.log'));
Schedule::command('app:delete-expired-tokens')->daily()->appendOutputTo(storage_path('logs/zoom-auto-delete-expierd-token.log'));


Artisan::command('schedule:run-continuously', function () {
    $this->info('Starting continuous scheduler...');

    while (true) {
        Artisan::call('schedule:run');
        sleep(60);
     }
})->purpose('Run scheduler continuously without cron');
