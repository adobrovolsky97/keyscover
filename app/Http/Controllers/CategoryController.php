<?php

namespace App\Http\Controllers;

use App\Http\Resources\Category\CategoryResource;
use App\Services\Category\Contracts\CategoryServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Response;

/**
 * Class CategoryController
 */
class CategoryController extends Controller
{
    /**
     * @var CategoryServiceInterface
     */
    private CategoryServiceInterface $categoryService;

    /**
     * @param CategoryServiceInterface $categoryService
     */
    public function __construct(CategoryServiceInterface $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection($this->categoryService->withCount(['products'])->getAll(['parent_id' => null]));
    }
}
