<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserNotification\StoreRequest;
use App\Http\Resources\UserNotification\UserNotificationResource;
use App\Services\UserNotification\Contracts\UserNotificationServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Response;

/**
 * Class UserNotificationController
 */
class UserNotificationController extends Controller
{
    /**
     * @var UserNotificationServiceInterface
     */
    protected UserNotificationServiceInterface $notificationService;

    /**
     * @param UserNotificationServiceInterface $notificationService
     */
    public function __construct(UserNotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return UserNotificationResource::collection($this->notificationService->getAllPaginated(['user_id' => auth()->id()]));
    }

    public function getCount(): JsonResponse
    {
        return Response::json([
            'count' => $this->notificationService->count(['user_id' => auth()->id(), 'is_read' => false])
        ]);
    }

    public function readAllNotifications(): JsonResponse
    {
        $this->notificationService->readAll();

        return Response::json();
    }

    /**
     * Create user notification
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $this->notificationService->create(array_merge($request->validated(), ['is_admin_notification' => true]));

        return Response::json();
    }
}
