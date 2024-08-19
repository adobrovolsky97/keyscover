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
     * @return string
     */
    protected function getRepositoryClass(): string
    {
        return VisitRepositoryInterface::class;
    }
}
