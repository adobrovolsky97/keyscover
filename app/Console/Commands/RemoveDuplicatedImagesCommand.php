<?php

namespace App\Console\Commands;

use App\Enums\Product\Media;
use App\Models\Product\Product;
use Illuminate\Console\Command;

class RemoveDuplicatedImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'duplicated-images:remove';

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
        foreach (Product::all() as $product) {
            $mediaNames = [];

            foreach ($product->refresh()->media as $media) {
                if (in_array($media->getCustomProperty('name'), $mediaNames)) {
                    $media->delete();
                    $this->info('Deleted duplicate media ' . $media->name . ' from product ' . $product->name);
                    continue;
                }

                $mediaNames[] = $media->getCustomProperty('name');
            }

            if ($product->refresh()->getFirstMedia(Media::COLLECTION_MAIN->value) === null) {
                $product->getFirstMedia()?->update(['collection_name' => Media::COLLECTION_MAIN->value]);
            }
        }
    }
}
