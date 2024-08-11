<?php

namespace App\Http\Resources\Delivery;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'   => $this->resource['Ref'],
            'name' => trim(($this->resource['StreetsType'] ?? '') . ' '. $this->resource['Description'])
        ];
    }
}
