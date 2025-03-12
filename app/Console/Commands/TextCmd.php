<?php

namespace App\Console\Commands;

use App\Models\ProductSubscription\ProductSubscription;
use Illuminate\Console\Command;

class TextCmd extends Command
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
    public function handle()
    {
        foreach (ProductSubscription::query()->get() as $sub){
            $sub->product->update(['left_in_stock' => 0]);
            $sub->product->refresh();
            $sub->product->update(['left_in_stock' => 5, 'is_available_in_stock' => true]);
        }
    }
}
