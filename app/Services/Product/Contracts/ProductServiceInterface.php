<?php

namespace App\Services\Product\Contracts;

use Adobrovolsky97\LaravelRepositoryServicePattern\Services\Contracts\BaseCrudServiceInterface;

/**
 * Interface ProductServiceInterface
 */
interface ProductServiceInterface extends BaseCrudServiceInterface
{
    /**
     * Handle mass action
     *
     * @param array $ids
     * @param string $action
     * @return void
     */
    public function handleMassAction(array $ids, string $action): void;
}
