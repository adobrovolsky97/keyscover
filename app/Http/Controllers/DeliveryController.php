<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Delivery\AddressResource;
use App\Http\Resources\Delivery\CityResource;
use App\Http\Resources\Delivery\WarehouseResource;
use App\Services\Delivery\Contracts\DeliveryServiceInterface;
use App\Services\Delivery\Drivers\DeliveryDriverInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class DeliveryController
 */
class DeliveryController extends Controller
{
    /**
     * @var DeliveryServiceInterface
     */
    private DeliveryServiceInterface $service;

    /**
     * @param DeliveryServiceInterface $service
     */
    public function __construct(DeliveryServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * @param Request $request
     * @param DeliveryDriverInterface $driver
     * @return AnonymousResourceCollection
     */
    public function cities(Request $request, DeliveryDriverInterface $driver): AnonymousResourceCollection
    {
        return CityResource::collection($this->service->setDriver($driver)->getCities(['search' => $request->get('search')]));
    }

    /**
     * @param string $cityId
     * @param Request $request
     * @param DeliveryDriverInterface $driver
     * @return AnonymousResourceCollection
     */
    public function warehouses(DeliveryDriverInterface $driver, string $cityId, Request $request): AnonymousResourceCollection
    {
        return WarehouseResource::collection($this->service->setDriver($driver)->getWarehouses([
            'search'  => $request->get('search'),
            'city_id' => $cityId
        ]));
    }

    /**
     * @param string $cityId
     * @param Request $request
     * @param DeliveryDriverInterface $driver
     * @return AnonymousResourceCollection
     */
    public function addresses(DeliveryDriverInterface $driver, string $cityId, Request $request): AnonymousResourceCollection
    {
        return AddressResource::collection($this->service->setDriver($driver)->getAddresses($cityId, ['search' => $request->get('search')]));
    }
}
