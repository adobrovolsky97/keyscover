<?php

namespace App\Console;

use App\Console\Commands\FetchCategoriesCommand;
use App\Console\Commands\FetchDollarCommand;
use App\Console\Commands\FetchProductsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(FetchCategoriesCommand::class)->hourly();
        $schedule->command(FetchProductsCommand::class)->everyFiveMinutes()->withoutOverlapping();
        $schedule->command(FetchDollarCommand::class)->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
