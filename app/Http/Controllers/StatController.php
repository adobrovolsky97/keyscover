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
     * Get unique visits count for date range
     *
     * @param VisitServiceInterface $visitService
     * @return JsonResponse
     */
    public function uniqueVisits(VisitServiceInterface $visitService): JsonResponse
    {
        return Response::json([
            'data' => $visitService->getUniqueVisitsCountForDateRange(today()->subDays(6), today())
        ]);
    }

    /**
     * Get visits count for today by hour
     *
     * @param VisitServiceInterface $visitService
     * @return JsonResponse
     */
    public function visitsByHour(VisitServiceInterface $visitService): JsonResponse
    {
        return Response::json([
            'data' => $visitService->getVisitsCountForTodayByHour()
        ]);
    }
}
