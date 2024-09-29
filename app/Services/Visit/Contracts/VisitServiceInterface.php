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
     * @param array $dates
     * @return array
     */
    public function getUniqueVisitsCountForDateRange(array $dates): array;

    /**
     * Get visits count for today by hour
     *
     * @param array $dates
     * @return array
     */
    public function getVisitsCountForDateRange(array $dates): array;

    /**
     * Get platform statistics
     *
     * @param array $dates
     * @return array
     */
    public function getPlatformStatistics(array $dates): array;

    /**
     * Get device statistics
     *
     * @param array $dates
     * @return array
     */
    public function getDeviceStatistics(array $dates): array;

    /**
     * Get browser statistics
     *
     * @param array $dates
     * @return array
     */
    public function getBrowserStatistics(array $dates): array;
}
