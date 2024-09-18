<?php

namespace App\Http\Resources\Cart;

use App\Enums\Product\Media;
use App\Models\Cart\Cart;
use App\Models\CartProduct\CartProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CartResource
 *
 * @mixin Cart
 */
class CartResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        if (empty($this->resource)) {
            return [];
        }

        return [
            'quantity'            => $this->quantity,
            'total_usd'           => $this->total,
            'total'               => $this->total_uah,
            'discount_percent'    => round($this->discount_percent),
            'discount_amount'     => $this->discount_amount,
            'discount_amount_uah' => $this->discount_amount_uah,
            'is_changed'          => $this->is_changed,
            'products'            => $this->products->map(function (CartProduct $cartProduct) {
                $image = $cartProduct->product->getFirstMediaUrl(Media::COLLECTION_MAIN->value, 'watermarked');
                return [
                    'id'              => $cartProduct->id,
                    'sku'             => $cartProduct->product->sku,
                    'name'            => $cartProduct->product->name,
                    'quantity'        => $cartProduct->quantity,
                    'price'           => $cartProduct->product->price,
                    'uah_price'       => ceil($cartProduct->product->uah_price),
                    'left_in_stock'   => $cartProduct->product->left_in_stock,
                    'image'           => empty($image) ? asset('no-image.png') : $image,
                    'product_id'      => $cartProduct->product_id,
                    'total_price_uah' => ceil($cartProduct->quantity * ceil($cartProduct->product->uah_price)),
                    'total_price_usd' => round($cartProduct->quantity * $cartProduct->product->price, 2),
                ];
            })
        ];
    }
}
