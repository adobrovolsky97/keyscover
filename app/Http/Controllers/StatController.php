<?php

namespace App\Http\Controllers;

use App\Services\Visit\Contracts\VisitServiceInterface;
use Illuminate\Http\JsonResponse;
use Response;

/**
 * Class StatController
 */
class StatController extends Controller
{
    /**
     * @var VisitServiceInterface
     */
    private VisitServiceInterface $visitService;

    /**
     * @param VisitServiceInterface $visitService
     */
    public function __construct(VisitServiceInterface $visitService)
    {
        $this->visitService = $visitService;
    }

    /**
     * Get unique visits count for date range
     *
     * @return JsonResponse
     */
    public function uniqueVisits(): JsonResponse
    {
        return Response::json([
            'data' => $this->visitService->getUniqueVisitsCountForDateRange(today()->subDays(6), today())
        ]);
    }

    /**
     * Get visits count for today by hour
     *
     * @return JsonResponse
     */
    public function visitsByHour(): JsonResponse
    {
        return Response::json([
            'data' => $this->visitService->getVisitsCountForTodayByHour()
        ]);
    }

    /**
     * Get platform statistics
     *
     * @return JsonResponse
     */
    public function platforms(): JsonResponse
    {
        return Response::json([
            'data' => $this->visitService->getPlatformStatistics()
        ]);
    }

    /**
     * Get device statistics
     *
     * @return JsonResponse
     */
    public function devices(): JsonResponse
    {
        return Response::json([
            'data' => $this->visitService->getDeviceStatistics()
        ]);
    }

    /**
     * Get browser statistics
     *
     * @return JsonResponse
     */
    public function browsers(): JsonResponse
    {
        return Response::json([
            'data' => $this->visitService->getBrowserStatistics()
        ]);
    }
}
