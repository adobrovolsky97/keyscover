<?php

namespace App\Http\Controllers;

use App\Enums\Config\Key;
use App\Http\Requests\Config\UpdateRequest;
use App\Http\Resources\Config\ConfigResource;
use App\Models\Config\Config;
use App\Services\Config\Contracts\ConfigServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Response;

/**
 * Class ConfigController
 */
class ConfigController extends Controller
{
    /**
     * @var ConfigServiceInterface
     */
    private ConfigServiceInterface $configService;

    /**
     * @param ConfigServiceInterface $configService
     */
    public function __construct(ConfigServiceInterface $configService)
    {
        $this->configService = $configService;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return Response::json([
            'usd'                   => $this->configService->getValue(Key::DOLLAR->value),
            'five_percent_discount' => $this->configService->getValue(Key::FIVE_PERCENT_DISCOUNT_SUM->value),
            'ten_percent_discount'  => $this->configService->getValue(Key::TEN_PERCENT_DISCOUNT_SUM->value),
            'free_delivery_sum'     => $this->configService->getValue(Key::FREE_DELIVERY_SUM->value),
        ]);
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function list(): AnonymousResourceCollection
    {
        return ConfigResource::collection($this->configService->getAll([['key', 'in', [Key::IS_CRM_ENABLED->value]]]));
    }

    /**
     * @param Config $config
     * @return ConfigResource
     */
    public function show(Config $config): ConfigResource
    {
        return ConfigResource::make($config);
    }

    /**
     * @param Config $config
     * @param UpdateRequest $request
     * @return ConfigResource
     */
    public function update(Config $config, UpdateRequest $request): ConfigResource
    {
        return ConfigResource::make($this->configService->update($config, $request->validated()));
    }
}
