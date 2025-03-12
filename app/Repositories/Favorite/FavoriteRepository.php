<?php

namespace App\Repositories\Favorite;

use App\Models\Favorite\Favorite;
use App\Repositories\Favorite\Contracts\FavoriteRepositoryInterface;
use Adobrovolsky97\LaravelRepositoryServicePattern\Repositories\BaseRepository;

/**
 * Class FavoriteRepository
 */
class FavoriteRepository extends BaseRepository implements FavoriteRepositoryInterface
{
	/**
	 * @return string
	 */
	protected function getModelClass(): string
	{
		return Favorite::class;
	}
}