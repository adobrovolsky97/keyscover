<?php

namespace App\Services\Cart;

use App\Enums\Config\Key;
use App\Models\Cart\Cart;
use App\Models\CartProduct\CartProduct;
use App\Models\Product\Product;
use App\Services\Cart\Contracts\CartServiceInterface;
use App\Repositories\Cart\Contracts\CartRepositoryInterface;
use Adobrovolsky97\LaravelRepositoryServicePattern\Services\BaseCrudService;
use App\Services\Config\Contracts\ConfigServiceInterface;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class CartService
 */
class CartService extends BaseCrudService implements CartServiceInterface
{
    /**
     * @return Model|null
     */
    public function getUserCart(): ?Cart
    {
        if (Auth::guest()) {
            return null;
        }

        return $this->repository->findFirst(['user_id' => Auth::id()]);
    }

    /**
     * Add product to cart
     *
     * @param Product $product
     * @param int $quantity
     * @return Cart
     * @throws Exception
     */
    public function addProductToCart(Product $product, int $quantity): Cart
    {
        if (empty($cart = $this->getUserCart())) {
            $cart = $this->repository->create(['user_id' => Auth::id()]);
        }

        /** @var CartProduct $cartProduct */
        $cartProduct = $cart->products->where('product_id', $product->id)->first();

        if ($product->left_in_stock < $quantity) {
            throw new BadRequestHttpException('В наявності: ' . $product->left_in_stock . ' штук.');
        }

        !empty($cartProduct)
            ? $cartProduct->update(['quantity' => $cartProduct->quantity + 1])
            : $cart->products()->create([
            'product_id' => $product->id,
            'quantity'   => $quantity,
        ]);

        $this->updateCartTotals($cart);

        return $cart->refresh();
    }

    /**
     * Update product quantity
     *
     * @param Product $product
     * @param int $quantity
     * @return Cart
     * @throws Exception
     */
    public function updateQuantity(Product $product, int $quantity): Cart
    {
        if ($quantity < 1) {
            throw new Exception('Quantity must be greater than 0');
        }

        if (empty($cart = $this->getUserCart())) {
            throw new Exception('Cart is empty');
        }

        /** @var CartProduct $cartProduct */
        $cartProduct = $cart->products()->where('product_id', $product->id)->firstOrFail();

        if ($product->left_in_stock < $quantity) {
            throw new BadRequestHttpException('В наявності: ' . $product->left_in_stock . ' штук.');
        }

        $cartProduct->update(['quantity' => $quantity]);

        $this->updateCartTotals($cart);

        return $cart->refresh();
    }

    /**
     * @param Cart $cart
     * @return void
     */
    protected function updateCartTotals(Cart $cart): void
    {
        $configService = app(ConfigServiceInterface::class);
        $dollarRate = $configService->getValue(Key::DOLLAR->value);
        $fivePercentConfigValue = $configService->getValue(Key::FIVE_PERCENT_DISCOUNT_SUM->value);
        $tenPercentConfigValue = $configService->getValue(Key::TEN_PERCENT_DISCOUNT_SUM->value);

        $totalPrice = 0;
        $totalPriceUah = 0;

        $cart->refresh()->products->each(function (CartProduct $cartProduct) use (&$totalPrice, &$totalPriceUah) {
            $totalPrice += round($cartProduct->quantity * $cartProduct->product->price, 2);
            $totalPriceUah += $cartProduct->quantity * $cartProduct->product->uah_price;
        });

        $discountPercent = 0;
        $discountAmount = 0;
        $discountAmountUah = 0;

        $totalPriceUah = floor($totalPriceUah);

        // calculating discounts
        if ($totalPrice >= $fivePercentConfigValue) {
            $discountPercent = 5;
            $discountAmount = $totalPrice * $discountPercent / 100;
            $discountAmountUah = round($totalPriceUah * $discountPercent / 100);
        }

        if ($totalPrice >= $tenPercentConfigValue) {
            $discountPercent = 10;
            $discountAmount = $totalPrice * $discountPercent / 100;
            $discountAmountUah = round($totalPriceUah * $discountPercent / 100);
        }

        $cart->update([
            'total'               => $totalPrice,
            'total_uah'           => $totalPriceUah,
            'dollar_rate'         => $dollarRate,
            'quantity'            => $cart->products->sum('quantity'),
            'discount_percent'    => $discountPercent,
            'discount_amount'     => $discountAmount,
            'discount_amount_uah' => $discountAmountUah,
        ]);
    }

    /**
     * Delete product from cart
     *
     * @param Product $product
     * @return Cart
     * @throws Exception
     */
    public function deleteProduct(Product $product): Cart
    {
        if (empty($cart = $this->getUserCart())) {
            throw new Exception('Cart is empty');
        }

        $cart->products()->where('product_id', $product->id)->delete();

        $this->updateCartTotals($cart);

        return $cart->refresh();
    }

    /**
     * @return void
     */
    public function clearCart(): void
    {
        $this->getUserCart()?->delete();
    }

    /**
     * @return string
     */
    protected function getRepositoryClass(): string
    {
        return CartRepositoryInterface::class;
    }
}
