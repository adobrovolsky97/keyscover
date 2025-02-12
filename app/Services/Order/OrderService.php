<?php

namespace App\Services\Order;

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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

            if (empty($cart) || empty($cart->products)) {
                throw new BadRequestHttpException(__('Кошик пустий'));
            }

            $data['user_id'] = Auth::id();
            $data['dollar_rate'] = $configService->getValue(Key::DOLLAR->value);
            $data['total_price_usd'] = $cart->total;
            $data['total_price_uah'] = $cart->total_uah;
            $data['discount_percent'] = $cart->discount_percent;
            $data['discount_usd'] = $cart->discount_amount;
            $data['discount_uah'] = $cart->discount_amount_uah;
            $data['number'] = $data['user_id'] . '-' . time();

            /** @var Order $order */
            $order = parent::create($data);

            /** @var CartProduct $product */
            foreach ($cart->products as $product) {
                if($product->product->left_in_stock < $product->quantity) {
                    throw new BadRequestHttpException(__('Товар :product_name недоступний в такій кількості', ['product_name' => $product->product->name]));
                }

                $order->products()->create([
                    'product_id'      => $product->product_id,
                    'quantity'        => $product->quantity,
                    'total_price'     => $product->quantity * $product->product->price,
                    'total_price_uah' => $product->quantity * $product->product->uah_price,
                ]);

                $product->product->update(['left_in_stock' => $product->product->left_in_stock - $product->quantity]);
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
