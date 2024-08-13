<?php

namespace App\Models\Product;

use App\Enums\Config\Key;
use App\Enums\Product\Media;
use App\Models\CartProduct\CartProduct;
use App\Services\Config\Contracts\ConfigServiceInterface;
use Carbon\Carbon;
use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Adobrovolsky97\LaravelRepositoryServicePattern\Models\BaseModel;
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
 * @property float $price
 * @property boolean $is_available_in_stock
 * @property integer $left_in_stock
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Category $category
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
        'left_in_stock',
        'created_at',
        'updated_at'
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
        return round(app(ConfigServiceInterface::class)->getValue(Key::DOLLAR->value) * $this->price);
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
        $this->addMediaCollection(Media::COLLECTION_MAIN->name);
        $this->addMediaCollection(Media::COLLECTION_ADDITIONAL->name);
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
            ->watermarkWidth(80, Manipulations::UNIT_PERCENT)
            ->watermarkHeight(30, Manipulations::UNIT_PERCENT)
            ->watermarkOpacity(35)
            ->nonQueued();
    }
}
