<?php

namespace App\Repositories\ProductSubscription;

use App\Models\ProductSubscription\ProductSubscription;
use Adobrovolsky97\LaravelRepositoryServicePattern\Repositories\BaseRepository;
use App\Repositories\ProductSubscription\Contracts\ProductSubscriptionRepositoryInterface;

/**
 * Class ProductSubscriptionRepository
 */
class ProductSubscriptionRepository extends BaseRepository implements ProductSubscriptionRepositoryInterface
{
	/**
	 * @return string
	 */
	protected function getModelClass(): string
	{
		return ProductSubscription::class;
	}
}