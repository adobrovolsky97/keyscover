<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanBackupsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backups:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old backups.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $backups = Storage::files('KeysCover');

        if (empty($backups)) {
            $this->info('No backups found.');
            return;
        }

        foreach ($backups as $backupFile) {
            $fileName = explode('/', $backupFile)[1];
            $fileDate = explode('.', $fileName)[0];

            $fileDate = Carbon::createFromFormat('Y-m-d-H-i-s', $fileDate);

            $fileDate->diffInDays(Carbon::now()) > 7
                ? Storage::delete($backupFile)
                : $this->info('Backup is not old enough to be deleted.');
        }
    }
}
