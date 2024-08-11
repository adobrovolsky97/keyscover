<?php

namespace App\Repositories\Order;

use App\Models\Order\Order;
use App\Repositories\Order\Contracts\OrderRepositoryInterface;
use Adobrovolsky97\LaravelRepositoryServicePattern\Repositories\BaseRepository;

/**
 * Class OrderRepository
 */
class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
	/**
	 * @return string
	 */
	protected function getModelClass(): string
	{
		return Order::class;
	}
}