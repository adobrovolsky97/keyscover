<?php

namespace App\Http\Resources\Product;

use App\Enums\Product\Media;
use App\Http\Resources\Media\MediaResource;
use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SimplifiedProductResource
 *
 * @mixin Product
 */
class SimplifiedProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $image = $this->getFirstMediaUrl(Media::COLLECTION_MAIN->value, 'watermarked');

        return [
            'id'                    => $this->id,
            'sku'                   => $this->sku,
            'name'                  => $this->name,
            'description'           => $this->description,
            'price'                 => ceil($this->uah_price),
            'usd_price'             => $this->price, // TODO
            'is_available_in_stock' => $this->is_available_in_stock,
            'is_hidden'             => $this->is_hidden,
            'left_in_stock'         => $this->left_in_stock,
            'image'                 => empty($image) ? asset('no-image.png') : $image,
            'media'                 => $this->whenLoaded('media', MediaResource::collection($this->media->sortByDesc('collection_name'))),
            'category'              => [
                'id'          => $this->category?->id,
                'name'        => $this->category?->name,
                'breadcrumbs' => $this->category?->breadcrumbs
            ],
        ];
    }
}