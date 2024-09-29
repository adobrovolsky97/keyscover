<?php

namespace App\Repositories\Visit\Contracts;

use Adobrovolsky97\LaravelRepositoryServicePattern\Repositories\Contracts\BaseRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Interface VisitRepositoryInterface
 */
interface VisitRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get unique visits count for date range
     *
     * @param Carbon $dateFrom
     * @param Carbon|null $dateTo
     * @return Collection
     */
    public function getUniqueVisitsCountForDateRange(Carbon $dateFrom, Carbon $dateTo = null): Collection;

    /**
     * Get visits count for today by hour
     *
     * @param array $dates
     * @return Collection
     */
    public function getVisitsCountForDateRange(array $dates): Collection;

    /**
     * Get grouped statistic by column
     *
     * @param string $column
     * @param array $dates
     * @return Collection
     */
    public function getGroupedStatisticByColumn(string $column, array $dates): Collection;
}
