<?php

namespace App\Observers;

use App\Jobs\SendProductAvailableEmailJob;
use App\Models\Product\Product;

class ProductObserver
{
    public function updated(Product $product): void
    {
        if ($product->activeSubscriptions->isEmpty()) {
            return;
        }

        $oldStock = $product->getOriginal('left_in_stock');
        if ($product->is_available_in_stock || $oldStock <= 0 && $product->left_in_stock > 0) {

            foreach ($product->activeSubscriptions as $activeSubscription) {
                SendProductAvailableEmailJob::dispatch($activeSubscription);
            }
        }
    }
}
