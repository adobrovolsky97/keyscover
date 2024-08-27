<?php

namespace App\Jobs;

use App\Models\Order\Order;
use App\Services\Crm\Contracts\CrmServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOrderToCrmJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Order
     */
    private Order $order;

    /**
     * @var string
     */
    public $queue = 'crm';

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(CrmServiceInterface $crmService): void
    {
        $crmService->createOrder($this->order);
    }
}
