<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\SimplifiedProductResource;
use App\Models\OrderProduct\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class OrderProductResource
 *
 * @mixin OrderProduct
 */
class OrderProductResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'product'         => SimplifiedProductResource::make($this->product),
            'quantity'        => $this->quantity,
            'total_price'     => $this->total_price,
            'total_price_uah' => $this->total_price_uah,
        ];
    }
}
