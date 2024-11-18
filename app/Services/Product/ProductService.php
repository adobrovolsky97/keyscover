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
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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

            $this->handleImages($product, $data['images'] ?? [], $data['images_to_remove'] ?? []);

            return $product->refresh();
        });
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    protected function handleImages(Product $product, array $images = [], array $imagesToRemove = []): void
    {
        if (!empty($imagesToRemove)) {
            $product->media()->whereIn('id', $imagesToRemove)->delete();
        }

        $idsForOrder = [];
        foreach ($images as $image) {
            if (!empty($image['id'])) {
                $idsForOrder[] = $image['id'];

                continue;
            }

            if (!empty($image['file']) && $image['file'] instanceof UploadedFile) {
                $idsForOrder[] = $product->addMedia($image['file'])->toMediaCollection(MediaEnum::COLLECTION_IMAGES->value)->getKey();
            }
        }

        if (!empty($idsForOrder)) {
            Media::setNewOrder($idsForOrder);
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
