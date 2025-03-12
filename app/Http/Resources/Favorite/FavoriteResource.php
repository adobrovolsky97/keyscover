<?php

namespace App\Http\Resources\Favorite;

use App\Http\Resources\Product\ProductResource;
use App\Models\Favorite\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class FavoriteResource
 *
 * @mixin Favorite
 */
class FavoriteResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return ProductResource::make($this->product)->toArray($request);
    }
}
