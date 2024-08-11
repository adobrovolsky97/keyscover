<?php

namespace App\Services\Config;

use App\Models\Config\Config;
use App\Services\Config\Contracts\ConfigServiceInterface;
use App\Repositories\Config\Contracts\ConfigRepositoryInterface;
use Adobrovolsky97\LaravelRepositoryServicePattern\Services\BaseCrudService;

/**
 * Class ConfigService
 */
class ConfigService extends BaseCrudService implements ConfigServiceInterface
{
    /**
     * Get value by key
     *
     * @param string $key
     * @return mixed
     */
    public function getValue(string $key): mixed
    {
        /** @var Config $config */
        $config = $this->repository->findOrFail($key, 'key');

        return $config->type->getConvertedValue($config->value);
    }

    /**
     * @return string
     */
    protected function getRepositoryClass(): string
    {
        return ConfigRepositoryInterface::class;
    }
}
