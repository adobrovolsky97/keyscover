<?php

namespace App\Repositories\Export;

use App\Models\Export\Export;
use App\Repositories\Export\Contracts\ExportRepositoryInterface;
use Adobrovolsky97\LaravelRepositoryServicePattern\Repositories\BaseRepository;

/**
 * Class ExportRepository
 */
class ExportRepository extends BaseRepository implements ExportRepositoryInterface
{
	/**
	 * @return string
	 */
	protected function getModelClass(): string
	{
		return Export::class;
	}
}