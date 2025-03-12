<?php

namespace App\Services\Favorite;

use Adobrovolsky97\LaravelRepositoryServicePattern\Exceptions\Service\ServiceException;
use App\Models\Favorite\Favorite;
use App\Models\Product\Product;
use App\Services\Favorite\Contracts\FavoriteServiceInterface;
use App\Repositories\Favorite\Contracts\FavoriteRepositoryInterface;
use Adobrovolsky97\LaravelRepositoryServicePattern\Services\BaseCrudService;

/**
 * Class FavoriteService
 */
class FavoriteService extends BaseCrudService implements FavoriteServiceInterface
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
    public function toggle(Product $product): ?Favorite
    {
        if (auth()->guest()) {
            abort(404);
        }

        $favorite = $product->favorites->where('user_id', auth()->id())->first();

        if (!empty($favorite)) {
            $favorite->delete();
            return null;
        }

        return $this->create(['product_id' => $product->id, 'user_id' => auth()->id()]);
    }

    /**
     * @return string
     */
    protected function getRepositoryClass(): string
    {
        return FavoriteRepositoryInterface::class;
    }
}
