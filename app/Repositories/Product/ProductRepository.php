<?php

namespace App\Repositories\Product;

use App\Models\Product\Product;
use App\Repositories\Product\Contracts\ProductRepositoryInterface;
use Adobrovolsky97\LaravelRepositoryServicePattern\Repositories\BaseRepository;
use DB;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ProductRepository
 */
class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    protected $with = ['media'];

    protected function applyFilters(array $searchParams = []): Builder
    {
        return $this
            ->applyFilterConditions($searchParams)
            ->when(!empty($searchParams['exclude']), function (Builder $query) use ($searchParams) {
                $query->whereNotIn('id', $searchParams['exclude']);
            })
            ->when(!empty($searchParams['search']), function (Builder $query) use ($searchParams) {
                $search = strtolower($searchParams['search']);
                $query->where(function (Builder $query) use ($search) {
                    $query
                        ->where(DB::raw('LOWER(name)'), 'like', '%' . $search . '%')
                        ->orWhere(DB::raw('LOWER(sku)'), 'like', '%' . $search . '%');
                });
            })
            ->when(!empty($searchParams['categories']), function (Builder $query) use ($searchParams) {
                $query->whereHas('category', function (Builder $query) use ($searchParams) {
                    $query->whereIn('slug', array_unique(explode(',', $searchParams['categories'])));
                });
            })
            ->when(!empty($searchParams['order_by']), function (Builder $query) use ($searchParams) {
                $orderByAndDir = explode('_', $searchParams['order_by']);

                match ($orderByAndDir[0]) {
                    'popularity' => $query->withCount('orderProducts')->orderBy('order_products_count', $orderByAndDir[1] ?? 'desc'),
                    default => $query->orderBy($orderByAndDir[0], $orderByAndDir[1] ?? 'desc'),
                };
            })
            ->when(!empty($searchParams['only_available']), function (Builder $query) use ($searchParams) {
                $query->where('left_in_stock', '>', 0);
            })
            ->when(!empty($searchParams['exclude_hidden']), function (Builder $query) use ($searchParams) {
                $query->where('is_hidden', false);
            })
            ->when(!empty($searchParams['sort_by']), function (Builder $query) use ($searchParams) {
                $query
                    ->reorder($searchParams['sort_by'], $searchParams['order_dir'] ?? 'asc')
                    ->orderBy('id');
            });
    }

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Product::class;
    }
}
