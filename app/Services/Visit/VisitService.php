<?php

namespace App\Services\Visit;

use App\Services\Visit\Contracts\VisitServiceInterface;
use App\Repositories\Visit\Contracts\VisitRepositoryInterface;
use Adobrovolsky97\LaravelRepositoryServicePattern\Services\BaseCrudService;
use Carbon\Carbon;

/**
 * Class VisitService
 * @property VisitRepositoryInterface $repository
 */
class VisitService extends BaseCrudService implements VisitServiceInterface
{
    /**
     * Get unique visits count for date range
     *
     * @param Carbon $dateFrom
     * @param Carbon $dateTo
     * @return array
     */
    public function getUniqueVisitsCountForDateRange(Carbon $dateFrom, Carbon $dateTo): array
    {
        return $this->repository->getUniqueVisitsCountForDateRange($dateFrom, $dateTo)->toArray();
    }

    /**
     * Get visits count for today by hour
     *
     * @return array
     */
    public function getVisitsCountForTodayByHour(): array
    {
        return $this->repository->getVisitsCountForTodayByHour()->toArray();
    }

    /**
     * Get platform statistics
     *
     * @return array
     */
    public function getPlatformStatistics(): array
    {
        return $this->calculatePercentage(
            $this->repository->getGroupedStatisticByColumn('os')->toArray()
        );
    }

    /**
     * Get device statistics
     *
     * @return array
     */
    public function getDeviceStatistics(): array
    {
        return $this->calculatePercentage(
            $this->repository->getGroupedStatisticByColumn('device')->toArray()
        );
    }

    /**
     * Get browser statistics
     *
     * @return array
     */
    public function getBrowserStatistics(): array
    {
        return $this->calculatePercentage(
            $this->repository->getGroupedStatisticByColumn('browser')->toArray()
        );
    }

    protected function calculatePercentage(array $data): array
    {
        $total = array_sum(array_column($data, 'count'));

        return array_map(function ($item) use ($total) {
            $item['percentage'] = round($item['count'] / $total * 100, 2);

            return $item;
        }, $data);
    }

    /**
     * @return string
     */
    protected function getRepositoryClass(): string
    {
        return VisitRepositoryInterface::class;
    }
}
