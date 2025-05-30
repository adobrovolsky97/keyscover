<?php

namespace App\Models\Category;

use Carbon\Carbon;
use App\Models\Product\Product;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Adobrovolsky97\LaravelRepositoryServicePattern\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Category
 *
 * @property integer $id
 * @property integer $external_id
 * @property string $name
 * @property string $slug
 * @property integer $parent_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Product[]|Collection $products
 * @property Category[]|Collection $children
 */
class Category extends BaseModel
{
    use Sluggable;

    /**
     * @var array
     */
    protected $fillable = ['external_id', 'slug', 'name', 'parent_id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Get the children categories products count (it can be multi layers).
     *
     * @return int
     */
    public function getChildrenCategoriesProductsCount(): int
    {
        $children = $this->children()->withCount('products')->get();
        $productsCount = 0;

        if ($children->isNotEmpty()) {
            foreach ($children as $child) {
                $productsCount += $child->getChildrenCategoriesProductsCount();
            }
        } else {
            $productsCount = $this->products->where('is_hidden', false)->count();
        }

        return $productsCount;
    }

    /**
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id', 'external_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id', 'external_id');
    }

    public function getBreadcrumbsAttribute(): string
    {
        $breadcrumbs = [];
        $category = $this;

        while (!empty($category->parent_id)) {
            $breadcrumbs[] = $category->name;
            $category = $category->parent;
        }

        $breadcrumbs[] = $category->name;

        return implode(' / ', array_reverse($breadcrumbs));
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}
