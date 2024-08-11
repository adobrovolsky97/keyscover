<?php

namespace App\Services\Delivery\Drivers;

use App\Http\Resources\Delivery\UkrPostCityResource;
use App\Http\Resources\Delivery\UkrPostWarehouseResource;
use Kolirt\Ukrposhta\Facade\Ukrposhta;

/**
 * Class UkrPostDriver
 */
class UkrPostDriver implements DeliveryDriverInterface
{
    /**
     * @param array $params
     * @return array
     */
    public function getCities(array $params = []): array
    {
        if (!isset($params['search']) || strlen($params['search'] < 2)) {
            return [];
        }
    }

    /**
     * @param array $params
     * @return array
     */
    public function getWarehouses(array $params = []): array
    {
        // TODO: Implement getWarehouses() method.
    }

    /**
     * @return string
     */
    public function getCityResource(): string
    {
        return UkrPostCityResource::class;
    }

    /**
     * @return string
     */
    public function getWarehouseResource(): string
    {
        return UkrPostWarehouseResource::class;
    }

    public function getAddresses(string $cityId, array $params = []): array
    {
        // TODO: Implement getAddresses() method.
    }
}
