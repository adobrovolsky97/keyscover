<?php

namespace App\Repositories\Order;

use Adobrovolsky97\LaravelRepositoryServicePattern\Exceptions\Repository\RepositoryException;
use App\Models\Order\Order;
use App\Repositories\Order\Contracts\OrderRepositoryInterface;
use Adobrovolsky97\LaravelRepositoryServicePattern\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class OrderRepository
 */
class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
    /**
     * @param array $searchParams
     * @return Builder
     * @throws RepositoryException
     */
    protected function applyFilters(array $searchParams = []): Builder
    {
        return parent::applyFilters($searchParams)
            ->when(!empty($searchParams['search']), function (Builder $query) use ($searchParams){
                $query->where(function (Builder $query) use ($searchParams){
                    $lowerSearch = mb_strtolower($searchParams['search']);
                    $query
                        ->where('surname', 'like', "%$lowerSearch%")
                        ->orWhere('name', 'like', "%$lowerSearch%")
                        ->orWhere('patronymic', 'like', "%$lowerSearch%")
                        ->orWhere('phone', 'like', '%'.str_replace('+38', '', $lowerSearch).'%');
                });
            })
            ->when(!empty($searchParams['sort_by']), function (Builder $query) use ($searchParams){
                $query->reorder($searchParams['sort_by'], $searchParams['order_dir'] ?? 'asc');
            });
    }

    /**
	 * @return string
	 */
	protected function getModelClass(): string
	{
		return Order::class;
	}
}
