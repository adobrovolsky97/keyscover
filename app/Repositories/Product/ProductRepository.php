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
        $query = parent::applyFilters($searchParams);

        $query
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
            });

        return $query;
    }

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Product::class;
    }
}
