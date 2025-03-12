<?php

namespace App\Http\Controllers;

use App\Models\Product\Product;
use App\Services\ProductSubscription\Contracts\ProductSubscriptionServiceInterface;
use Illuminate\Http\JsonResponse;
use Response;

/**
 * Class ProductSubscriptionController
 */
class ProductSubscriptionController extends Controller
{
    /**
     * @var ProductSubscriptionServiceInterface
     */
    protected ProductSubscriptionServiceInterface $service;

    /**
     * @param ProductSubscriptionServiceInterface $service
     */
    public function __construct(ProductSubscriptionServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * @param Product $product
     * @return JsonResponse
     */
    public function store(Product $product): JsonResponse
    {
        if (auth()->guest()) {
            abort(404);
        }

        $data = [
            'product_id' => $product->id,
            'user_id'    => auth()->id()
        ];

        $this->service->updateOrCreate($data, $data);

        return Response::json();
    }
}
