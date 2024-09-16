<?php

namespace App\Console\Commands;

use App\Enums\Product\Media;
use App\Models\Category\Category;
use App\Models\Product\Product;
use App\Services\Category\Contracts\CategoryServiceInterface;
use App\Services\Crm\Contracts\CrmServiceInterface;
use App\Services\Product\Contracts\ProductServiceInterface;
use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;
use Throwable;

class FetchProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch products from CRM and save them to the database';

    /**
     * @var CategoryServiceInterface
     */
    protected CategoryServiceInterface $categoryService;

    /**
     * @var ProductServiceInterface
     */
    private ProductServiceInterface $productService;

    public function __construct(CategoryServiceInterface $categoryService, ProductServiceInterface $productService)
    {
        $this->categoryService = $categoryService;
        $this->productService = $productService;

        parent::__construct();
    }

    /**
     * Execute the console command.
     * @throws FileCannotBeAdded
     */
    public function handle(CrmServiceInterface $crmService): void
    {
        $page = 1;
        $data = $crmService->getProductsList(page: $page);
        $lastPage = $data['pagination']['total_pages'] ?? null;

        if ($lastPage === null) {
            $this->error('Failed to fetch products');
            return;
        }

        while ($page <= $lastPage) {
            foreach ($data['items'] ?? [] as $product) {
                $this->processProduct($product);
            }

            $page++;
            $data = $crmService->getProductsList(page: $page);
        }
    }

    /**
     * @param array $productData
     * @return void
     * @throws FileCannotBeAdded
     */
    protected function processProduct(array $productData): void
    {
        /** @var Category $category */
        $category = !empty($productData['category_id'])
            ? $this->categoryService->updateOrCreate(['external_id' => $productData['category_id']], [
                'name'      => $productData['category']['name'],
                'parent_id' => $productData['category']['parent_id'] ?? null
            ])
            : null;

        /** @var Product $product */
        $product = $this->productService->updateOrCreate(
            ['sku' => $productData['sku']],
            [
                'external_id'           => $productData['id'],
                'name'                  => $productData['title'],
                'category_id'           => $category?->id,
                'price'                 => $productData['price_amount'],
                'is_available_in_stock' => ($productData['stock_available'] ?? 0) > 0,
                'left_in_stock'         => $productData['stock_available'] ?? 0,
                'custom_fields'         => $productData['custom_fields'] ?? [],
            ]
        );

        $this->info('Product ' . $product->name . ' has been processed');

        if (!empty($productData['asset_url'])) {
            $this->addAttachmentsToProduct($product, array_merge(
                [$productData['asset_url']],
                array_column($productData['attachments'] ?? [], 'original_url'),
            ));
        }
    }

    /**
     * Add attachments to product
     *
     * @param Product $product
     * @param array $attachments
     * @return void
     * @throws FileCannotBeAdded
     */
    protected function addAttachmentsToProduct(Product $product, array $attachments): void
    {
        $attachmentNames = [];

        foreach ($attachments as $attachment) {
            $exploded = explode('/', $attachment);
            $attachmentNames[] = end($exploded);
        }

        foreach ($product->getMedia('*') as $media) {
//            if (!in_array($media->getCustomProperty('name'), $attachmentNames)) {
//                $media->delete();
//                $this->warn('Attachment ' . $media->getCustomProperty('name') . ' has been removed from product ' . $product->name);
//                continue;
//            }

            if (in_array($media->getCustomProperty('name'), $attachmentNames)) {
                unset($attachments[array_search($media->getCustomProperty('name'), $attachmentNames)]);
                $this->warn('Attachment ' . $media->getCustomProperty('name') . ' already exists in product ' . $product->name);
            }
        }

        foreach ($attachments as $id => $attachment) {
            try {
                $exploded = explode('/', $attachment);
                $name = end($exploded);
                /** @var \Spatie\MediaLibrary\MediaCollections\Models\Media $media */
                $product
                    ->addMediaFromUrl($attachment)
                    ->withCustomProperties(['name' => $name])
                    ->toMediaCollection($id === 0 ? Media::COLLECTION_MAIN->value : Media::COLLECTION_ADDITIONAL->value);

                $this->info('Attachment ' . $name . ' has been added to product ' . $product->name);
            } catch (Throwable $exception) {
                $this->error('Failed to add attachment ' . $name . ' to product ' . $product->name . ' ' . $exception->getMessage());
            }
        }
    }
}
