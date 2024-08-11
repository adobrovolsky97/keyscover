<?php

namespace App\Models\OrderProduct;

use App\Models\Order\Order;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Adobrovolsky97\LaravelRepositoryServicePattern\Models\BaseModel;

/**
 * Class OrderProduct
 *
 * @property integer $order_id
 * @property integer $product_id
 * @property integer $quantity
 * @property float $total_price
 * @property integer $total_price_uah
 * @property Product $product
 * @property Order $order
 */
class OrderProduct extends BaseModel
{
	/**
	 * @var string
	 */
	public $timestamps = false;

	/**
	 * @var boolean
	 */
	public $incrementing = false;

	/**
	 * @var array
	 */
	protected $primaryKey = ['order_id', 'product_id'];

	/**
	 * @var string
	 */
	protected $keyType = 'array';

	/**
	 * @var array
	 */
	protected $fillable = ['order_id', 'product_id', 'quantity', 'total_price', 'total_price_uah'];

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
	public function order(): BelongsTo
	{
		return $this->belongsTo(Order::class, 'order_id', 'id');
	}
}
