<?php

namespace App\Http\Resources\Config;

use App\Models\Config\Config;
use App\Services\Config\Contracts\ConfigServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ConfigResource
 *
 * @mixin Config
 */
class ConfigResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'key'   => $this->key,
            'value' => $this->value,
            'type'  => $this->type,
        ];
    }
}
