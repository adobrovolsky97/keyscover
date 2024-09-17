<?php

namespace App\Console\Commands;

use App\Jobs\SendOrderToCrmJob;
use App\Models\Order\Order;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

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

            if ($order->created_at->diffInMinutes(now()) < 5) {
                $this->warn('Order #' . $order->number . ' was created less than 5 minutes ago');
                continue;
            }

            if (!app()->isLocal()) {

                Notification::route('telegram', config('services.telegram-bot-api.recipient'))
                    ->notify(new OrderCreatedNotification($order));

                $this->info('Order #' . $order->number . ' was sent to CRM');
                SendOrderToCrmJob::dispatch($order)->onQueue('crm');
            }
        }
    }
}
