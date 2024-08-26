<?php

namespace App\Repositories\Visit;

use Adobrovolsky97\LaravelRepositoryServicePattern\Exceptions\Repository\RepositoryException;
use App\Models\Visit\Visit;
use App\Repositories\Visit\Contracts\VisitRepositoryInterface;
use Adobrovolsky97\LaravelRepositoryServicePattern\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Class VisitRepository
 */
class VisitRepository extends BaseRepository implements VisitRepositoryInterface
{
    /**
     * Get unique visits count for date range
     *
     * @param Carbon $dateFrom
     * @param Carbon $dateTo
     * @return Collection
     * @throws RepositoryException
     */
    public function getUniqueVisitsCountForDateRange(Carbon $dateFrom, Carbon $dateTo): Collection
    {
        return $this
            ->applyFilterConditions([])
            ->whereBetween('date', [$dateFrom->format('Y-m-d'), $dateTo->format('Y-m-d')])
            ->selectRaw('count(distinct ip) as count, date')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get visits count for today by hour
     *
     * @return Collection
     * @throws RepositoryException
     */
    public function getVisitsCountForTodayByHour(): Collection
    {
        return $this
            ->applyFilterConditions([])
            ->where('date', today()->format('Y-m-d'))
            ->selectRaw('count(id) as count, HOUR(created_at) as hour')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    /**
     * Get grouped statistic by column
     *
     * @param string $column
     * @return Collection
     */
    public function getGroupedStatisticByColumn(string $column): Collection
    {
        return $this->getQuery()
            ->selectRaw("count(*) as count, $column")
            ->groupBy($column)
            ->whereNotNull($column)
            ->get();
    }

    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Visit::class;
    }
}
