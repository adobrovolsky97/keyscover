<?php

namespace App\Console\Commands;

use App\Jobs\SendOrderToCrmJob;
use App\Models\Order\Order;
use Illuminate\Console\Command;

class ReSyncOrderToCrm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:re-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        foreach (Order::query()->whereNull('crm_order_id')->get() as $order) {
            if (!app()->isLocal()) {
                $this->info('Order #' . $order->number . ' was sent to CRM');
                SendOrderToCrmJob::dispatch($order)->onQueue('crm');
            }
        }
    }
}
