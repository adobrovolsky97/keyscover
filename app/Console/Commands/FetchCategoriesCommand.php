<?php

namespace App\Console\Commands;

use App\Services\Category\Contracts\CategoryServiceInterface;
use App\Services\Crm\Contracts\CrmServiceInterface;
use Illuminate\Console\Command;

class FetchCategoriesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categories:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch categories from external API.';

    /**
     * Execute the console command.
     */
    public function handle(CrmServiceInterface $crmService, CategoryServiceInterface $categoryService): void
    {
        $page = 1;
        $data = $crmService->getCategoriesList(page: $page);
        $lastPage = $data['pagination']['total_pages'] ?? null;

        if ($lastPage === null) {
            $this->error('Failed to fetch products');
            return;
        }

        while ($page <= $lastPage) {
            foreach ($data['items'] ?? [] as $category) {
                $categoryService->updateOrCreate(
                    ['external_id' => $category['id']],
                    ['name' => $category['name'], 'parent_id' => $category['parent_id'] ?? null]
                );
                $this->info('Category ' . $category['name'] . ' has been fetched.');
            }

            $page++;
            $data = $crmService->getCategoriesList(page: $page);
        }
    }
}
