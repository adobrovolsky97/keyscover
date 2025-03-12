<?php

namespace App\Models\ProductSubscription;

use Adobrovolsky97\LaravelRepositoryServicePattern\Models\BaseModel;
use App\Models\Product\Product;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ProductSubscription
 *
 * @property int $id
 * @property int $user_id
 * @property int $product_id
 * @property boolean $is_reminder_sent
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property User $user
 * @property Product $product
 */
class ProductSubscription extends BaseModel
{
    protected $fillable = [
        'user_id',
        'product_id',
        'is_reminder_sent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
