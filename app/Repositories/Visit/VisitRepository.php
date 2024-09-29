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
     * @param Carbon|null $dateTo
     * @return Collection
     * @throws RepositoryException
     */
    public function getUniqueVisitsCountForDateRange(Carbon $dateFrom, Carbon $dateTo = null): Collection
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
     * @param array $dates
     * @return Collection
     * @throws RepositoryException
     */
    public function getVisitsCountForDateRange(array $dates): Collection
    {
        // if range is 1 day - then by hour, else by day
        $groupBy = Carbon::parse($dates['start_date'])->diffInDays(Carbon::parse($dates['end_date'])) < 2 ? 'HOUR' : 'DAY';

        return $this
            ->applyFilterConditions([])
            ->whereBetween('date', [
                Carbon::parse($dates['start_date']),
                !empty($dates['end_date']) ? Carbon::parse($dates['end_date']) : Carbon::now()
            ])
            ->when($groupBy === 'HOUR', function ($query) {
                return $query->selectRaw('count(id) as count, HOUR(created_at) as label');
            }, function ($query) {
                return $query->selectRaw('count(id) as count, date as label');
            })
            ->groupBy('label')
            ->orderBy('label')
            ->get();
    }

    /**
     * Get grouped statistic by column
     *
     * @param string $column
     * @param array $dates
     * @return Collection
     */
    public function getGroupedStatisticByColumn(string $column, array $dates): Collection
    {
        return $this->getQuery()
            ->selectRaw("count(*) as count, $column")
            ->whereBetween('date', [
                Carbon::parse($dates['start_date']),
                !empty($dates['end_date']) ? Carbon::parse($dates['end_date']) : Carbon::now()
            ])
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
