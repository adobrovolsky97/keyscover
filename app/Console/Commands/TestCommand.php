<?php

namespace App\Console\Commands;

use App\Jobs\SendOrderToCrmJob;
use App\Models\Order\Order;
use App\Services\Crm\Contracts\CrmServiceInterface;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(CrmServiceInterface $crmService)
    {
        $order = Order::query()->latest('id')->first();

        SendOrderToCrmJob::dispatch($order);
    }
}
