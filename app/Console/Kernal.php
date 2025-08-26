<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Existing commands...
        // $schedule->command('inspire')->hourly();



    // $schedule->command('zoom:sessions:auto-create')->everyMinute();
    // $schedule->command('zoom:sessions:auto-end')->everyMinute();

    //     // يمكنك إضافة المزيد من المهام
    //     $schedule->command('queue:work --stop-when-empty')->everyMinute();
    //     $schedule->command('model:prune')->daily();



    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
