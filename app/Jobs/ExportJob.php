<?php

namespace App\Jobs;

use App\Enums\Export\Status;
use App\Exports\User\UserExport;
use App\Models\Export\Export;
use App\Models\Order\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
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
            if (!Storage::disk('public')->exists('exports')) {
                Storage::disk('public')->makeDirectory('exports');
            }

            $this->processExport();

            $this->export->update(['status' => Status::COMPLETED, 'file_path' => "exports/{$this->export->name}"]);

        } catch (Throwable $exception) {
            $this->export->update([
                'error'  => $exception->getMessage(),
                'status' => Status::FAILED
            ]);
        }
    }

    /**
     * @throws Exception
     */
    protected function processExport(): void
    {
        if ($this->export->type === Export::TYPE_USERS) {
            Excel::store(new UserExport(), "exports/{$this->export->name}", 'public');
            return;
        }

        if(empty($this->export->params['order_id'])){
            throw new Exception('Order ID is required');
        }

        $order = Order::findOrFail($this->export->params['order_id']);

        $pdf = PDF::loadView('pdf.order', ['order' => $order])
            ->setPaper('a4')
            ->setOptions(['isHtml5ParserEnabled' => true, 'isPhpEnabled' => true]);

        $pdf->save(Storage::disk('public')->path("exports/{$this->export->name}"));
    }
}
