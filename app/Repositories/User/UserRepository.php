<?php

namespace App\Repositories\User;

use Adobrovolsky97\LaravelRepositoryServicePattern\Exceptions\Repository\RepositoryException;
use App\Models\User\User;
use App\Repositories\User\Contracts\UserRepositoryInterface;
use Adobrovolsky97\LaravelRepositoryServicePattern\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class UserRepository
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @param array $searchParams
     * @return Builder
     * @throws RepositoryException
     */
    protected function applyFilters(array $searchParams = []): Builder
    {
        return $this
            ->applyFilterConditions($searchParams)
            ->when(!empty($searchParams['except']), fn(Builder $query) => $query->whereNotIn('id', $searchParams['except']))
            ->when(!empty($searchParams['role']), fn(Builder $query) => $query->where('role', $searchParams['role']))
            ->orderBy('id', 'desc');
    }

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return User::class;
    }
}
