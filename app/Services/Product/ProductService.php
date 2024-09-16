<?php

namespace App\Services\Product;

use App\Enums\Product\Media as MediaEnum;
use App\Models\Product\Product;
use App\Services\Product\Contracts\ProductServiceInterface;
use App\Repositories\Product\Contracts\ProductRepositoryInterface;
use Adobrovolsky97\LaravelRepositoryServicePattern\Services\BaseCrudService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

/**
 * Class ProductService
 */
class ProductService extends BaseCrudService implements ProductServiceInterface
{
    public function update($keyOrModel, array $data): ?Model
    {
        return DB::transaction(function () use ($keyOrModel, $data) {
            /** @var Product $product */
            $product = parent::update($keyOrModel, $data);

            $this->handleImages($product, $data['images'] ?? [], $data['images_to_remove'] ?? []);

            return $product->refresh();
        });
    }

    /**
     * Handle product images
     *
     * @param Product $product
     * @param array $images
     * @param array $imagesToRemove
     * @return void
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    protected function handleImages(Product $product, array $images = [], array $imagesToRemove = []): void
    {
        if (empty($images) && empty($imagesToRemove)) {
            return;
        }

        if (!empty($imagesToRemove)) {
            $product->media()->whereIn('id', $imagesToRemove)->delete();
        }

        $product->refresh()->media()->update(['collection_name' => MediaEnum::COLLECTION_ADDITIONAL->value]);

        /** @var UploadedFile $image */
        foreach ($images as $image) {

            if ($product->media()->where('file_name', $image->getClientOriginalName())->exists()) {
                continue;
            }

            $product->addMedia($image)->withCustomProperties(['name' => $image->getClientOriginalName()])->toMediaCollection(MediaEnum::COLLECTION_ADDITIONAL->value);
        }

        $firstMedia = $product->refresh()->getFirstMedia(MediaEnum::COLLECTION_ADDITIONAL->value);

        if (!empty($firstMedia)) {
            $firstMedia->update(['collection_name' => MediaEnum::COLLECTION_MAIN->value]);
        }
    }

    /**
     * @return string
     */
    protected function getRepositoryClass(): string
    {
        return ProductRepositoryInterface::class;
    }
}
