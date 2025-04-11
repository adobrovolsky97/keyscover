<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\MassActionRequest;
use App\Http\Requests\Product\SearchRequest;
use App\Http\Requests\Product\UpdateRequest;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product\Product;
use App\Services\Product\Contracts\ProductServiceInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class ProductController
 */
class ProductController extends Controller
{
    /**
     * @var ProductServiceInterface
     */
    private ProductServiceInterface $productService;

    /**
     * @param ProductServiceInterface $productService
     */
    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @param SearchRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(SearchRequest $request): AnonymousResourceCollection
    {
        return ProductResource::collection(
            $this->productService
                ->with(['activeUserSubscription', 'category', 'similarProducts', 'relatedProducts', 'activeUserFavorite'])
                ->getAllPaginated($request->validated(), $request->input('per_page', 20))
        );
    }

    /**
     * @param Product $product
     * @return ProductResource
     */
    public function show(Product $product): ProductResource
    {
        return ProductResource::make($product->load('media'));
    }

    /**
     * Handle mass actions
     *
     * @param MassActionRequest $request
     * @return JsonResponse
     */
    public function handleMassActions(MassActionRequest $request): JsonResponse
    {
        $this->productService->handleMassAction($request->input('ids'), $request->input('action'));

        return response()->json(null, 204);
    }

    /**
     * Update product
     *
     * @param Product $product
     * @param UpdateRequest $request
     * @return ProductResource
     */
    public function update(Product $product, UpdateRequest $request): ProductResource
    {
        return ProductResource::make($this->productService->update($product, $request->validated()));
    }

    /**
     * Delete product
     *
     * @param Product $product
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->productService->delete($product);

        return response()->json(null, 204);
    }
}
