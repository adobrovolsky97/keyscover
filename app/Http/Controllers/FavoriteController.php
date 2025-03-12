<?php

namespace App\Http\Controllers;

use Adobrovolsky97\LaravelRepositoryServicePattern\Exceptions\Service\ServiceException;
use App\Http\Resources\Favorite\FavoriteResource;
use App\Models\Product\Product;
use App\Services\Favorite\Contracts\FavoriteServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Response;

/**
 * Class FavoriteController
 */
class FavoriteController extends Controller
{
    /**
     * @var FavoriteServiceInterface
     */
    protected FavoriteServiceInterface $favoriteService;

    /**
     * @param FavoriteServiceInterface $favoriteService
     */
    public function __construct(FavoriteServiceInterface $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return FavoriteResource::collection($this->favoriteService->getAllPaginated(['user_id' => auth()->id()]));
    }

    /**
     * @param Product $product
     * @return JsonResponse
     * @throws ServiceException
     */
    public function toggle(Product $product): JsonResponse
    {
        $this->favoriteService->toggle($product);
        return Response::json();
    }
}
