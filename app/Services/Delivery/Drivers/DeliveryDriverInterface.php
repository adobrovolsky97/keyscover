<?php

namespace App\Services\Delivery\Drivers;

/**
 * Interface DeliveryDriverInterface
 */
interface DeliveryDriverInterface
{
    /**
     * @param array $params
     * @return array
     */
    public function getCities(array $params = []): array;

    /**
     * @param array $params
     * @return array
     */
    public function getWarehouses(array $params = []): array;

    /**
     * @param string $cityId
     * @param array $params
     * @return array
     */
    public function getAddresses(string $cityId, array $params = []): array;
}
