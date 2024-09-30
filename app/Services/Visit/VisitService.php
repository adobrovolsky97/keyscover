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
     * @param array $dates
     * @return array
     */
    public function getUniqueVisitsCountForDateRange(array $dates): array
    {
        $startDate = Carbon::parse($dates['start_date']);
        $endDate = !empty($dates['end_date']) ? Carbon::parse($dates['end_date']) : Carbon::now();
        return $this->repository->getUniqueVisitsCountForDateRange($startDate, $endDate)->toArray();
    }

    /**
     * Get visits count for today by hour
     *
     * @param array $dates
     * @return array
     */
    public function getVisitsCountForDateRange(array $dates): array
    {
        return $this->repository->getVisitsCountForDateRange($dates)->toArray();
    }

    /**
     * Get platform statistics
     *
     * @param array $dates
     * @return array
     */
    public function getPlatformStatistics(array $dates): array
    {
        return $this->calculatePercentage(
            $this->repository->getGroupedStatisticByColumn('os', $dates)->toArray()
        );
    }

    /**
     * Get device statistics
     *
     * @param array $dates
     * @return array
     */
    public function getDeviceStatistics(array $dates): array
    {
        $data = $this->calculatePercentage(
            $this->repository->getGroupedStatisticByColumn('device', $dates)->toArray()
        );

        foreach ($data as &$item) {
            $item['device'] = match ($item['device']) {
                'Nexus' => 'Android',
                '0' => 'Bot',
                'WebKit' => 'Windows PC',
                'Macintosh' => 'Mac',
                default => $item['device'],
            };
        }

        return $data;
    }

    /**
     * Get browser statistics
     *
     * @param array $dates
     * @return array
     */
    public function getBrowserStatistics(array $dates): array
    {
        return $this->calculatePercentage(
            $this->repository->getGroupedStatisticByColumn('browser', $dates)->toArray()
        );
    }

    /**
     * @param array $data
     * @return array
     */
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
