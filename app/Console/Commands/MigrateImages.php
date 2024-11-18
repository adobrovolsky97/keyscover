<?php

namespace App\Console\Commands;

use App\Enums\Product\Media;
use App\Models\Product\Product;
use Illuminate\Console\Command;

class MigrateImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-images';

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
        /** @var Product $product */
        foreach (Product::all() as $id => $product) {
          $media = $product->getMedia('*');
          $mediaIds = $media->pluck('id')->toArray();

          \Spatie\MediaLibrary\MediaCollections\Models\Media::query()->whereIn('id', $mediaIds)->update([
             'collection_name' => Media::COLLECTION_IMAGES->value
          ]);

          \Spatie\MediaLibrary\MediaCollections\Models\Media::setNewOrder($mediaIds);
        }
    }
}
