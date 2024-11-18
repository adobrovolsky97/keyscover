<?php

namespace App\Http\Resources\Product;

use App\Enums\Product\Media;
use App\Http\Resources\Media\MediaResource;
use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProductResource
 *
 * @mixin Product
 */
class ProductResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $image = $this->getFirstMediaUrl(Media::COLLECTION_IMAGES->value, 'watermarked');

        $fieldsToRender = ['Тип', 'Лезо', 'Кількість кнопок', 'Логотип', 'Частота'];

        $customFields = [];
        foreach ($this->custom_fields ?? [] as $key => $value) {
            if (in_array($key, $fieldsToRender) && !empty($value)) {
                $customFields[] = [
                    'key'   => $key,
                    'value' => $value
                ];
            }
        }

        return [
            'id'                    => $this->id,
            'sku'                   => $this->sku,
            'name'                  => $this->name,
            'description'           => $this->description,
            'price'                 => ceil($this->uah_price),
            'usd_price'             => $this->price, // TODO
            'is_available_in_stock' => $this->is_available_in_stock,
            'is_hidden'             => $this->is_hidden,
            'is_stop_crm_update'    => $this->is_stop_crm_update,
            'left_in_stock'         => $this->left_in_stock,
            'last_sync_at'          => $this->last_sync_at?->format('Y-m-d H:i:s'),
            'related_products'      => SimplifiedProductResource::collection($this->relatedProducts),
            'similar_products'      => SimplifiedProductResource::collection($this->similarProducts),
            'image'                 => empty($image) ? asset('no-image.png') : $image,
            'media'                 => $this->whenLoaded('media', MediaResource::collection($this->getMedia(Media::COLLECTION_IMAGES->value))),
            'category'              => [
                'id'          => $this->category?->id,
                'name'        => $this->category?->name,
                'breadcrumbs' => $this->category?->breadcrumbs
            ],
            'custom_fields'         => $customFields,
            'cart_increment_step'   => $this->cart_increment_step,
        ];
    }
}
