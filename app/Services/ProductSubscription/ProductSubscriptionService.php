<?php

namespace App\Services\ProductSubscription;

use Adobrovolsky97\LaravelRepositoryServicePattern\Services\BaseCrudService;
use App\Services\ProductSubscription\Contracts\ProductSubscriptionServiceInterface;
use App\Repositories\ProductSubscription\Contracts\ProductSubscriptionRepositoryInterface;

/**
 * Class ProductSubscriptionService
 */
class ProductSubscriptionService extends BaseCrudService implements ProductSubscriptionServiceInterface
{
	/**
	 * @return string
	 */
	protected function getRepositoryClass(): string
	{
		return ProductSubscriptionRepositoryInterface::class;
	}
}