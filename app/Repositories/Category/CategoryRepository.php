<?php

namespace App\Repositories\Category;

use App\Models\Category\Category;
use App\Repositories\Category\Contracts\CategoryRepositoryInterface;
use Adobrovolsky97\LaravelRepositoryServicePattern\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CategoryRepository
 */
class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    /**
     * @var string[]
     */
    protected $with = ['children'];

    protected $defaultOrderBy = 'name';

    protected function applyFilters(array $searchParams = []): Builder
    {
        return $this
            ->applyFilterConditions($searchParams)
            ->when(
                !is_array(($orderByField = $this->defaultOrderBy ?? app($this->getModelClass())->getKeyName())),
                function (Builder $query) use ($orderByField) {
                    $query->orderBy($orderByField);
                });
    }

	/**
	 * @return string
	 */
	protected function getModelClass(): string
	{
		return Category::class;
	}
}
