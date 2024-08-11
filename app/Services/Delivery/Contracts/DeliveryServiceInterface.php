<?php

namespace App\Services\Delivery\Contracts;

use App\Services\Delivery\Drivers\DeliveryDriverInterface;

/**
 * Interface DeliveryServiceInterface
 */
interface DeliveryServiceInterface
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

    /**
     * Set delivery driver
     *
     * @param DeliveryDriverInterface $deliveryDriver
     * @return self
     */
    public function setDriver(DeliveryDriverInterface $deliveryDriver): self;

}
