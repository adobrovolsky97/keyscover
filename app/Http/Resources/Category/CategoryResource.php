<?php

namespace App\Http\Resources\Category;

use App\Models\Category\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CategoryResource
 *
 * @mixin Category
 */
class CategoryResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $children = CategoryResource::collection($this->children->sortBy('name'));

        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'products_count' => $this->getChildrenCategoriesProductsCount(),
            'children'       => $children
        ];
    }
}
