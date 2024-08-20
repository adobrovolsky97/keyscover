<?php

namespace App\Http\Resources\Media;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Class MediaResource
 *
 * @mixin Media
 */
class MediaResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id'  => $this->id,
            'url' => $this->getUrl('watermarked')
        ];
    }
}