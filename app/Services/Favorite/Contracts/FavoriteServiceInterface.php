<?php

namespace App\Services\Favorite\Contracts;

use Adobrovolsky97\LaravelRepositoryServicePattern\Exceptions\Service\ServiceException;
use Adobrovolsky97\LaravelRepositoryServicePattern\Services\Contracts\BaseCrudServiceInterface;
use App\Models\Favorite\Favorite;
use App\Models\Product\Product;

/**
 * Interface FavoriteServiceInterface
 */
interface FavoriteServiceInterface extends BaseCrudServiceInterface
{
    /**
     * Toggle favorite
     *
     * @param Product $product
     *
     * @return Favorite|null
     *
     * @throws ServiceException
     */
    public function toggle(Product $product): ?Favorite;
}
