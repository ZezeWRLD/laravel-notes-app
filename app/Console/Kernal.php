<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        // Register your custom commands here
    ];


    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected function schedule(Schedule $schedule)
{
    // Send daily digest emails at 8 AM
    $schedule->command('email:send-digests daily')
        ->dailyAt('08:00')
        ->timezone('America/New_York');

    // Send weekly summary emails on Monday at 9 AM
    $schedule->command('email:send-digests weekly')
        ->weeklyOn(1, '09:00')
        ->timezone('America/New_York');

    // Retry failed emails every hour
    $schedule->command('email:retry-failed')
        ->hourly();

    // Clean up old emails monthly
    $schedule->command('email:cleanup')
        ->monthly();
}

}
