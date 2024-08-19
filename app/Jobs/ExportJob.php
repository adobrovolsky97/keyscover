<?php

namespace App\Jobs;

use App\Enums\Export\Status;
use App\Exports\User\UserExport;
use App\Models\Export\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;
use Storage;
use Throwable;

class ExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Export
     */
    private Export $export;

    /**
     * Create a new job instance.
     * @param Export $export
     */
    public function __construct(Export $export)
    {
        $this->export = $export;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $fileName = $this->export->name . '_' . now()->format('Y-m-d H:i:s') . '.xlsx';

            if (!Storage::disk('public')->exists('exports')) {
                Storage::disk('public')->makeDirectory('exports');
            }

            Excel::store(new UserExport(), "exports/$fileName", 'public');

            $this->export->update(['status' => Status::COMPLETED, 'file_path' => "exports/$fileName"]);

        } catch (Throwable $exception) {
            $this->export->update([
                'error'  => $exception->getMessage(),
                'status' => Status::FAILED
            ]);
        }
    }
}
