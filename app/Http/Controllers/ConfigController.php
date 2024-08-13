<?php

namespace App\Http\Controllers;

use App\Enums\Config\Key;
use App\Services\Config\Contracts\ConfigServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

/**
 * Class ConfigController
 */
class ConfigController extends Controller
{
    /**
     * @param ConfigServiceInterface $configService
     * @return JsonResponse
     */
    public function index(ConfigServiceInterface $configService): JsonResponse
    {
        return Response::json([
            'usd' => $configService->getValue(Key::DOLLAR->value),
            'five_percent_discount' => $configService->getValue(Key::FIVE_PERCENT_DISCOUNT_SUM->value),
            'ten_percent_discount' => $configService->getValue(Key::TEN_PERCENT_DISCOUNT_SUM->value),
            'free_delivery_sum' => $configService->getValue(Key::FREE_DELIVERY_SUM->value),
        ]);
    }
}
