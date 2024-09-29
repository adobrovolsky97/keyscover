<?php

namespace App\Http\Controllers;

use App\Http\Requests\Stat\StatRequest;
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
     * @param StatRequest $request
     * @return JsonResponse
     */
    public function uniqueVisits(StatRequest $request): JsonResponse
    {
        return Response::json([
            'data' => $this->visitService->getUniqueVisitsCountForDateRange($request->validated())
        ]);
    }

    /**
     * Get non-unique visits count for date range
     *
     * @param StatRequest $request
     * @return JsonResponse
     */
    public function nonUniqueVisits(StatRequest $request): JsonResponse
    {
        return Response::json([
            'data' => $this->visitService->getVisitsCountForDateRange($request->validated())
        ]);
    }

    /**
     * Get platform statistics
     *
     * @param StatRequest $request
     * @return JsonResponse
     */
    public function platforms(StatRequest $request): JsonResponse
    {
        return Response::json([
            'data' => $this->visitService->getPlatformStatistics($request->validated())
        ]);
    }

    /**
     * Get device statistics
     *
     * @param StatRequest $request
     * @return JsonResponse
     */
    public function devices(StatRequest $request): JsonResponse
    {
        return Response::json([
            'data' => $this->visitService->getDeviceStatistics($request->validated())
        ]);
    }

    /**
     * Get browser statistics
     *
     * @param StatRequest $request
     * @return JsonResponse
     */
    public function browsers(StatRequest $request): JsonResponse
    {
        return Response::json([
            'data' => $this->visitService->getBrowserStatistics($request->validated())
        ]);
    }
}
