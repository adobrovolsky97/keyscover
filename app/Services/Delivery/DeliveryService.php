<?php

namespace App\Services\Delivery;

use App\Services\Delivery\Contracts\DeliveryServiceInterface;
use App\Services\Delivery\Drivers\DeliveryDriverInterface;
use Exception;

/**
 * Class DeliveryService
 */
class DeliveryService implements DeliveryServiceInterface
{
    /**
     * @var DeliveryDriverInterface|null
     */
    protected ?DeliveryDriverInterface $deliveryDriver = null;

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function getCities(array $params = []): array
    {
        $this->validateDriver();

        return $this->deliveryDriver->getCities($params);
    }

    /**
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function getWarehouses(array $params = []): array
    {
        $this->validateDriver();

        return $this->deliveryDriver->getWarehouses($params);
    }

    /**
     * @param string $cityId
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function getAddresses(string $cityId, array $params = []): array
    {
        $this->validateDriver();

        return $this->deliveryDriver->getAddresses($cityId, $params);
    }

    /**
     * Set delivery driver
     *
     * @param DeliveryDriverInterface $deliveryDriver
     * @return self
     */
    public function setDriver(DeliveryDriverInterface $deliveryDriver): self
    {
        $this->deliveryDriver = $deliveryDriver;

        return $this;
    }

    /**
     * @throws Exception
     */
    protected function validateDriver(): void
    {
        if ($this->deliveryDriver === null) {
            throw new Exception('Driver is not set');
        }
    }
}
