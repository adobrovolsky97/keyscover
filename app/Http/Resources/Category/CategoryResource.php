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
        $children = CategoryResource::collection($this->children()->withCount('products')->orderBy('name')->get());

        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'products_count' => $children->collection->isNotEmpty() ? $children->collection->sum('products_count') : $this->products_count,
            'children'       => $children
        ];
    }
}
