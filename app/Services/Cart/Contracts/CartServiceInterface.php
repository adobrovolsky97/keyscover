<?php

namespace App\Services\Cart\Contracts;

use Adobrovolsky97\LaravelRepositoryServicePattern\Services\Contracts\BaseCrudServiceInterface;
use App\Models\Cart\Cart;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface CartServiceInterface
 */
interface CartServiceInterface extends BaseCrudServiceInterface
{
    /**
     * Get user cart
     *
     * @return Model|null
     */
    public function getUserCart(): ?Cart;

    /**
     * Add product to cart
     *
     * @param Product $product
     * @param int $quantity
     * @return Cart
     */
    public function addProductToCart(Product $product, int $quantity): Cart;

    /**
     * Update product quantity
     *
     * @param Product $product
     * @param int $quantity
     * @return Cart
     */
    public function updateQuantity(Product $product, int $quantity): Cart;

    /**
     * Delete product from cart
     *
     * @param Product $product
     * @return Cart
     */
    public function deleteProduct(Product $product): Cart;

    /**
     * @return void
     */
    public function clearCart(): void;
}
