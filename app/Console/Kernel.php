<?php

namespace App\Console;

use App\Console\Commands\BackupCommand;
use App\Console\Commands\CleanBackupsCommand;
use App\Console\Commands\FetchCategoriesCommand;
use App\Console\Commands\FetchDollarCommand;
use App\Console\Commands\FetchProductsCommand;
use App\Console\Commands\ReSyncOrderToCrm;
use App\Console\Commands\SendCrmDisabledNotificationCommand;
use App\Console\Commands\SyncOrderStatusesCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(FetchCategoriesCommand::class)->everyFiveMinutes();
        $schedule->command(FetchProductsCommand::class)->everyFiveMinutes()->withoutOverlapping();
        $schedule->command(FetchDollarCommand::class)->everyFiveMinutes();
        $schedule->command(BackupCommand::class)->daily();
        $schedule->command(CleanBackupsCommand::class)->daily();
        $schedule->command(ReSyncOrderToCrm::class)->hourly();
        $schedule->command(SendCrmDisabledNotificationCommand::class)->twiceDaily();
        $schedule->command(SyncOrderStatusesCommand::class)->everyFiveMinutes()->withoutOverlapping();
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
