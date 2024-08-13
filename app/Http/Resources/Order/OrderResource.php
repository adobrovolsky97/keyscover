<?php

namespace App\Http\Resources\Order;

use App\Models\Order\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class OrderResource
 *
 * @mixin Order
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'surname'                 => $this->surname,
            'name'                    => $this->name,
            'patronymic'              => $this->patronymic,
            'phone'                   => $this->phone,
            'delivery_type'           => $this->delivery_type,
            'comment'                 => $this->comment,
            'city_id'                 => $this->city_id,
            'city_name'               => $this->city_name,
            'warehouse_id'            => $this->warehouse_id,
            'warehouse_name'          => $this->warehouse_name,
            'total_price_usd'         => $this->total_price_usd,
            'total_price_uah'         => $this->total_price_uah,
            'discount_percent'        => round($this->discount_percent),
            'discount_usd'            => $this->discount_usd,
            'discount_uah'            => $this->discount_uah,
            'total_with_discount_uah' => round($this->total_price_uah - $this->discount_uah),
            'total_with_discount_usd' => round($this->total_price_usd - $this->discount_usd, 2),
            'created_at'              => $this->created_at->format('d.m.Y H:i:s'),
            'products'                => OrderProductResource::collection($this->products)
        ];
    }
}
