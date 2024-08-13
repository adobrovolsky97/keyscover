<?php

namespace App\Models\Cart;

use Carbon\Carbon;
use App\Models\User\User;
use App\Models\CartProduct\CartProduct;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Adobrovolsky97\LaravelRepositoryServicePattern\Models\BaseModel;

/**
 * Class Cart
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $quantity
 * @property float $total
 * @property integer $total_uah
 * @property float $discount_percent
 * @property float $discount_amount
 * @property integer $discount_amount_uah
 * @property float $dollar_rate
 * @property boolean $is_changed
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property User $user
 * @property CartProduct[]|Collection $products
 */
class Cart extends BaseModel
{
    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'quantity',
        'total',
        'total_uah',
        'discount_percent',
        'discount_amount',
        'discount_amount_uah',
        'dollar_rate',
        'is_changed',
        'created_at',
        'updated_at'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(CartProduct::class, 'cart_id', 'id');
    }
}
