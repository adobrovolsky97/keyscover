<?php

namespace App\Models\Product;

use App\Enums\Config\Key;
use App\Enums\Product\Media;
use App\Models\CartProduct\CartProduct;
use App\Models\OrderProduct\OrderProduct;
use App\Services\Config\Contracts\ConfigServiceInterface;
use Carbon\Carbon;
use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Adobrovolsky97\LaravelRepositoryServicePattern\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use League\Glide\Filesystem\FileNotFoundException;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Class Product
 *
 * @property integer $id
 * @property integer $category_id
 * @property integer $external_id
 * @property string $sku
 * @property string $name
 * @property string $description
 * @property boolean $is_stop_crm_update
 * @property boolean $is_hidden
 * @property float $price
 * @property array $custom_fields
 * @property boolean $is_available_in_stock
 * @property integer $left_in_stock
 * @property integer $cart_increment_step
 * @property Carbon $last_sync_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Category $category
 * @property OrderProduct[]|Collection $orderProducts
 * @property Product[]|Collection $relatedProducts
 * @property Product[]|Collection $similarProducts
 */
class Product extends BaseModel implements HasMedia
{
    use InteractsWithMedia;

    /**
     * @var array
     */
    protected $fillable = [
        'category_id',
        'external_id',
        'sku',
        'name',
        'price',
        'is_available_in_stock',
        'is_hidden',
        'left_in_stock',
        'custom_fields',
        'is_stop_crm_update',
        'last_sync_at',
        'description',
        'cart_increment_step',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'last_sync_at'  => 'datetime'
    ];

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    /**
     * @return float
     */
    public function getUahPriceAttribute(): float
    {
        return app(ConfigServiceInterface::class)->getValue(Key::DOLLAR->value) * $this->price;
    }

    /**
     * @return HasMany
     */
    public function orderProducts(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'product_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function cartProducts(): HasMany
    {
        return $this->hasMany(CartProduct::class, 'product_id', 'id');
    }

    /**
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(Media::COLLECTION_IMAGES->name);
    }

    /**
     * @return BelongsToMany
     */
    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'related_products', 'product_id', 'related_product_id');
    }

    /**
     * @return BelongsToMany
     */
    public function similarProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'similar_products', 'product_id', 'similar_product_id');
    }

    /**
     * @throws InvalidManipulation
     * @throws FileNotFoundException
     */
    public function registerMediaConversions(\Spatie\MediaLibrary\MediaCollections\Models\Media $media = null): void
    {
        $this->addMediaConversion('watermarked')
            ->performOnCollections('*')
            ->watermark(public_path('watermark.png'))
            ->watermarkPosition(Manipulations::POSITION_CENTER)
            ->watermarkWidth(100, Manipulations::UNIT_PERCENT)
            ->watermarkHeight(100, Manipulations::UNIT_PERCENT)
            ->watermarkOpacity(35)
            ->format(Manipulations::FORMAT_WEBP)
            ->queued();
    }
}
