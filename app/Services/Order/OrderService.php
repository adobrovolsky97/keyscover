<?php

namespace App\Services\Order;

use Adobrovolsky97\LaravelRepositoryServicePattern\Exceptions\Service\ServiceException;
use App\Enums\Config\Key;
use App\Models\CartProduct\CartProduct;
use App\Models\Order\Order;
use App\Services\Cart\Contracts\CartServiceInterface;
use App\Services\Config\Contracts\ConfigServiceInterface;
use App\Services\Order\Contracts\OrderServiceInterface;
use App\Repositories\Order\Contracts\OrderRepositoryInterface;
use Adobrovolsky97\LaravelRepositoryServicePattern\Services\BaseCrudService;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrderService
 */
class OrderService extends BaseCrudService implements OrderServiceInterface
{
    /**
     * @param array $data
     * @return Model|null
     */
    public function create(array $data): ?Model
    {
        return DB::transaction(function () use ($data) {
            $cartService = app(CartServiceInterface::class);
            $configService = app(ConfigServiceInterface::class);

            $cart = $cartService->getUserCart();

            if (empty($cart)) {
                throw new ServiceException(__('Cart is empty'));
            }

            $data['user_id'] = Auth::id();
            $data['dollar_rate'] = $configService->getValue(Key::DOLLAR->value);
            $data['total_price_usd'] = $cart->total;
            $data['total_price_uah'] = round($cart->total * $data['dollar_rate']);
            $data['discount_percent'] = $cart->discount_percent;
            $data['discount_usd'] = $cart->discount_amount;
            $data['discount_uah'] = $cart->discount_amount_uah;

            /** @var Order $order */
            $order = parent::create($data);

            /** @var CartProduct $product */
            foreach ($cart->products as $product) {
                $order->products()->create([
                    'product_id'      => $product->product_id,
                    'quantity'        => $product->quantity,
                    'total_price'     => $product->quantity * $product->product->price,
                    'total_price_uah' => round(($product->quantity * $product->product->price) * $data['dollar_rate']),
                ]);
            }

            $cartService->clearCart();

            return $order->refresh();
        });
    }

    /**
     * @return string
     */
    protected function getRepositoryClass(): string
    {
        return OrderRepositoryInterface::class;
    }
}
