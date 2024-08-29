<?php

namespace App\Http\Controllers;

use App\Enums\Role\Role;
use App\Http\Requests\Order\StoreRequest;
use App\Http\Resources\Order\OrderResource;
use App\Jobs\SendOrderToCrmJob;
use App\Notifications\OrderCreatedNotification;
use App\Services\Order\Contracts\OrderServiceInterface;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Notification;

/**
 * Class OrderController
 */
class OrderController extends Controller
{
    /**
     * @var OrderServiceInterface
     */
    private OrderServiceInterface $orderService;

    /**
     * @param OrderServiceInterface $orderService
     */
    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Get orders list
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return OrderResource::collection($this->orderService->getAllPaginated(
            Auth::user()->role === Role::ADMIN ? [] : ['user_id' => auth()->id()]
        ));
    }

    /**
     * Store order
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $order = $this->orderService->create($request->validated());

        Notification::route('telegram', config('services.telegram-bot-api.recipient'))
            ->notify(new OrderCreatedNotification($order));

        if (!app()->isLocal()) {
            SendOrderToCrmJob::dispatch($order)->onQueue('crm');
        }

        return response()->json();
    }
}
