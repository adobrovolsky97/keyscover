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
    const ACTION_DELETE = 'delete';
    const ACTION_HIDE = 'hide';
    const ACTION_SHOW = 'show';

    /**
     * Handle mass action
     *
     * @param array $ids
     * @param string $action
     * @return void
     */
    public function handleMassAction(array $ids, string $action): void
    {
        DB::transaction(function () use ($ids, $action) {
            match ($action) {
                self::ACTION_DELETE => $this->deleteMany($ids),
                self::ACTION_HIDE => $this->massHide($ids),
                self::ACTION_SHOW => $this->massShow($ids),
                default => null,
            };
        });
    }

    /**
     * Mass hide products
     *
     * @param array $ids
     * @return void
     */
    public function massHide(array $ids): void
    {
        $this->repository
            ->findMany([['id', 'IN', $ids]])
            ->where('is_hidden', false)
            ->each(function ($product) {
                $product->update(['is_hidden' => true]);
            });
    }

    /**
     * Mass show products
     *
     * @param array $ids
     * @return void
     */
    public function massShow(array $ids): void
    {
        $this->repository
            ->findMany([['id', 'IN', $ids]])
            ->where('is_hidden', true)
            ->each(function ($product) {
                $product->update(['is_hidden' => false]);
            });
    }

    /**
     * @param $keyOrModel
     * @param array $data
     * @return Model|null
     */
    public function update($keyOrModel, array $data): ?Model
    {
        return DB::transaction(function () use ($keyOrModel, $data) {
            /** @var Product $product */
            $product = parent::update($keyOrModel, $data);

            $product->relatedProducts()->sync($data['related_products'] ?? []);
            $product->similarProducts()->sync($data['similar_products'] ?? []);

            $this->handleImages(
                $product,
                $data['images'] ?? [],
                $data['images_to_remove'] ?? [],
                $data['main_image_index'] ?? null
            );

            return $product->refresh();
        });
    }

    /**
     * Handle product images
     *
     * @param Product $product
     * @param array $images
     * @param array $imagesToRemove
     * @param int|null $mainImageIndex
     * @return void
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    protected function handleImages(Product $product, array $images = [], array $imagesToRemove = [], int $mainImageIndex = null): void
    {
        if (empty($images) && empty($imagesToRemove) && is_null($mainImageIndex)) {
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


        if (is_null($mainImageIndex)) {
            // Just set first media as main one
            $product->refresh()->getFirstMedia()?->update(['collection_name' => MediaEnum::COLLECTION_MAIN->value]);
            return;
        }

        foreach ($product->media as $index => $media) {
            if ($index === $mainImageIndex) {
                $media->update(['collection_name' => MediaEnum::COLLECTION_MAIN->value]);
                break;
            }
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
