<?php

namespace App\Models\CartProduct;

use Carbon\Carbon;
use App\Models\Cart\Cart;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Adobrovolsky97\LaravelRepositoryServicePattern\Models\BaseModel;

/**
 * Class CartProduct
 * 
 * @property integer $id
 * @property integer $cart_id
 * @property integer $product_id
 * @property integer $quantity
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Product $product
 * @property Cart $cart
 */
class CartProduct extends BaseModel
{
	/**
	 * @var array
	 */
	protected $fillable = ['cart_id', 'product_id', 'quantity', 'created_at', 'updated_at'];

	/**
	 * @return BelongsTo
	 */
	public function product(): BelongsTo
	{
		return $this->belongsTo(Product::class, 'product_id', 'id');
	}

	/**
	 * @return BelongsTo
	 */
	public function cart(): BelongsTo
	{
		return $this->belongsTo(Cart::class, 'cart_id', 'id');
	}
}