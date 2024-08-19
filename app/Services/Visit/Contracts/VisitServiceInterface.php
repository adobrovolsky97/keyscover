<?php

namespace App\Services\Visit\Contracts;

use Adobrovolsky97\LaravelRepositoryServicePattern\Services\Contracts\BaseCrudServiceInterface;
use Carbon\Carbon;

/**
 * Interface VisitServiceInterface
 */
interface VisitServiceInterface extends BaseCrudServiceInterface
{
    /**
     * Get unique visits count for date range
     *
     * @param Carbon $dateFrom
     * @param Carbon $dateTo
     * @return array
     */
    public function getUniqueVisitsCountForDateRange(Carbon $dateFrom, Carbon $dateTo): array;

    /**
     * Get visits count for today by hour
     *
     * @return array
     */
    public function getVisitsCountForTodayByHour(): array;
}
