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
        $image = $this->getFirstMediaUrl(Media::COLLECTION_MAIN->value, 'watermarked');

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
            'price'                 => ceil($this->uah_price),
            'usd_price'             => $this->price, // TODO
            'is_available_in_stock' => $this->is_available_in_stock,
            'left_in_stock'         => $this->left_in_stock,
            'image'                 => empty($image) ? asset('no-image.png') : $image,
            'media'                 => $this->whenLoaded('media', MediaResource::collection($this->media)),
            'category'              => [
                'id'   => $this->category?->id,
                'name' => $this->category?->name,
            ],
            'custom_fields'         => $customFields
        ];
    }
}
