<?php

namespace App\Services\Crm\Contracts;

use App\Models\Order\Order;

/**
 * Interface CrmServiceInterface
 */
interface CrmServiceInterface
{
    /**
     * Get products list
     *
     * @param int|null $category
     * @param int $page
     * @return array
     */
    public function getProductsList(int $category = null, int $page = 1): array;

    /**
     * Get categories list
     *
     * @param int $page
     * @return array
     */
    public function getCategoriesList(int $page = 1): array;

    /**
     * Create crm order
     *
     * @param Order $order
     * @return void
     */
    public function createOrder(Order $order): void;

    /**
     * @param array $params
     * @param int $page
     * @return array
     */
    public function getOrders(array $params = [], int $page = 1): array;
}
