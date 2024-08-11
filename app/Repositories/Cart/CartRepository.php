<?php

namespace App\Repositories\Cart;

use App\Models\Cart\Cart;
use App\Repositories\Cart\Contracts\CartRepositoryInterface;
use Adobrovolsky97\LaravelRepositoryServicePattern\Repositories\BaseRepository;

/**
 * Class CartRepository
 */
class CartRepository extends BaseRepository implements CartRepositoryInterface
{
	/**
	 * @return string
	 */
	protected function getModelClass(): string
	{
		return Cart::class;
	}
}