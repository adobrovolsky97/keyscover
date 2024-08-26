<?php

namespace App\Http\Controllers;

use App\Http\Requests\VisitTracker\VisitTrackerRequest;
use App\Services\Visit\Contracts\VisitServiceInterface;
use Illuminate\Http\JsonResponse;
use Jenssegers\Agent\Agent;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VisitTrackerController
 */
class VisitTrackerController extends Controller
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
     * @param VisitTrackerRequest $request
     * @return JsonResponse
     */
    public function track(VisitTrackerRequest $request, Agent $agent): JsonResponse
    {
        $agent->setUserAgent($request->userAgent());

        $this->visitService->create([
            'ip'         => $request->ip(),
            'os'         => $agent->platform(),
            'browser'    => $agent->browser(),
            'device'     => $agent->device(),
            'url'        => $request->input('url'),
            'user_agent' => $request->userAgent(),
            'user_id'    => $request->user()?->id,
            'date'       => today()
        ]);

        return \Response::json(null, Response::HTTP_NO_CONTENT);
    }
}
