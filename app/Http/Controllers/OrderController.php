<?php

namespace App\Http\Controllers;

use App\Enums\Role\Role;
use App\Http\Requests\Order\SearchRequest;
use App\Http\Requests\Order\StoreRequest;
use App\Http\Resources\Order\OrderResource;
use App\Jobs\SendOrderToCrmJob;
use App\Mail\NewOrderMail;
use App\Models\Order\Order;
use App\Notifications\OrderCreatedNotification;
use App\Services\Order\Contracts\OrderServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Mail;
use Notification;
use Throwable;

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
     * @param SearchRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(SearchRequest $request): AnonymousResourceCollection
    {
        $params = $request->validated();

        if (Auth::user()->role !== Role::ADMIN) {
            $params['user_id'] = auth()->id();
        }

        return OrderResource::collection($this->orderService->getAllPaginated($params));
    }

    /**
     * Store order
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        /** @var Order $order */
        $order = $this->orderService->create($request->validated());

        if (!app()->isLocal()) {
            Mail::to($order->user->email)->send(new NewOrderMail($order));

            foreach (explode(',', config('services.telegram-bot-api.recipients')) as $recipient) {
                Notification::route('telegram', trim($recipient))
                    ->notify(new OrderCreatedNotification($order));
            }

            SendOrderToCrmJob::dispatch($order)->onQueue('crm');
        }

        return response()->json();
    }

    /**
     * @param Order $order
     * @return JsonResponse
     */
    public function syncOrderToCrm(Order $order): JsonResponse
    {
        if ($order->is_crm_synced) {
            abort(404);
        }

        try {
            SendOrderToCrmJob::dispatchSync($order);
            return response()->json();
        } catch (Throwable $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }
}
