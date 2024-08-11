<?php

namespace App\Http\Resources\Delivery;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class NewPostWarehouseResource
 */
class WarehouseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->resource['Ref'],
            'name' => $this->resource['Description']
        ];
    }
}
