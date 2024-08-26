<?php

namespace App\Console\Commands;

use App\Jobs\SendOrderToCrmJob;
use App\Models\Order\Order;
use App\Models\Product\Product;
use App\Services\Crm\Contracts\CrmServiceInterface;
use App\Services\Visit\Contracts\VisitServiceInterface;
use Carbon\Carbon;
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
    public function handle(VisitServiceInterface $visitService)
    {
        $product = Product::findOrFail(2);

        $mediaUrl = $product->getFirstMediaUrl('main', 'watermarked');
        if (empty($mediaUrl)) {
            $mediaUrl = $product->getFallbackMediaUrl('main');
        }
        dd($mediaUrl);
//
//        $visitService->getUniqueVisitsCountForDateRange(Carbon::parse('2024-08-01'), Carbon::parse('2024-08-31'));
    }
}
