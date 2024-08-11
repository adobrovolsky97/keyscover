<?php

namespace App\Repositories\Config;

use App\Models\Config\Config;
use App\Repositories\Config\Contracts\ConfigRepositoryInterface;
use Adobrovolsky97\LaravelRepositoryServicePattern\Repositories\BaseRepository;

/**
 * Class ConfigRepository
 */
class ConfigRepository extends BaseRepository implements ConfigRepositoryInterface
{
	/**
	 * @return string
	 */
	protected function getModelClass(): string
	{
		return Config::class;
	}
}