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
     * @param Carbon $dateTo
     * @return Collection
     */
    public function getUniqueVisitsCountForDateRange(Carbon $dateFrom, Carbon $dateTo): Collection;

    /**
     * Get visits count for today by hour
     *
     * @return Collection
     */
    public function getVisitsCountForTodayByHour(): Collection;
}
