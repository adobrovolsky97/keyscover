<?php

namespace App\Services\Config\Contracts;

use Adobrovolsky97\LaravelRepositoryServicePattern\Services\Contracts\BaseCrudServiceInterface;

/**
 * Interface ConfigServiceInterface
 */
interface ConfigServiceInterface extends BaseCrudServiceInterface
{
    /**
     * Get value by key
     *
     * @param string $key
     * @return mixed
     */
    public function getValue(string $key): mixed;
}
