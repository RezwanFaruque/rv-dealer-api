<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sync:modified')
            ->everyFiveMinutes()
            ->appendOutputTo( storage_path() . '/logs/schedule/sync_modified.log');

        $schedule->command('cache:counts')
            ->hourly()
            ->appendOutputTo( storage_path() . '/logs/schedule/cache_counts.log');
        $schedule->command('cache:statistics')
            ->hourly()
            ->appendOutputTo( storage_path() . '/logs/schedule/cache_statistics.log');

        $schedule->command('sync:deleted')
            ->hourly()
            ->appendOutputTo( storage_path() . '/logs/schedule/sync_deleted.log');


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
