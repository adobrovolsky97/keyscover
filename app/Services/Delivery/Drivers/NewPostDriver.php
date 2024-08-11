<?php

namespace App\Services\Delivery\Drivers;

use Daaner\NovaPoshta\Models\Address;

/**
 * Class NewPostDriver
 */
class NewPostDriver implements DeliveryDriverInterface
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

        return (new Address())->getCities($params['search'])['result'] ?? [];
    }

    /**
     * @param string $cityId
     * @param array $params
     * @return array
     */
    public function getAddresses(string $cityId, array $params = []): array
    {
        if (!isset($params['search']) || strlen($params['search'] < 2)) {
            return [];
        }

        return (new Address())->getStreet($cityId, $params['search'])['result'] ?? [];
    }

    /**
     * @param array $params
     * @return array
     */
    public function getWarehouses(array $params = []): array
    {
        if (!isset($params['city_id'])) {
            return [];
        }

        return (new Address())->getWarehouses($params['city_id'], false)['result'] ?? [];
    }
}
