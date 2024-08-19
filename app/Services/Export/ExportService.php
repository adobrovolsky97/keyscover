<?php

namespace App\Services\Export;

use App\Services\Export\Contracts\ExportServiceInterface;
use App\Repositories\Export\Contracts\ExportRepositoryInterface;
use Adobrovolsky97\LaravelRepositoryServicePattern\Services\BaseCrudService;

/**
 * Class ExportService
 */
class ExportService extends BaseCrudService implements ExportServiceInterface
{
	/**
	 * @return string
	 */
	protected function getRepositoryClass(): string
	{
		return ExportRepositoryInterface::class;
	}
}