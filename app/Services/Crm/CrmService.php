<?php

namespace App\Services\Crm;

use App\Models\Order\Order;
use App\Services\Crm\Contracts\CrmServiceInterface;
use Exception;
use Illuminate\Support\Facades\Http;

/**
 * Class CrmService
 */
class CrmService implements CrmServiceInterface
{
    /**
     * @const
     */
    private const API_URL = 'https://api.keepincrm.com/v1';

    /**
     * @throws Exception
     */
    public function __construct()
    {
        if (empty(config('app.crm_api_key'))) {
            throw new Exception('CRM API key is not set');
        }
    }

    /**
     * Get products list
     *
     * @param int|null $category
     * @param int $page
     * @return array
     */
    public function getProductsList(int $category = null, int $page = 1): array
    {
        $response = Http::withHeader('X-Auth-Token', config('app.crm_api_key'))
            ->get(self::API_URL . '/materials', [
                urlencode('q[category_id_eq]') => $category,
                'page'                         => $page
            ]);

        return $response->json();
    }

    /**
     * Get categories list
     *
     * @param int $page
     * @return array
     */
    public function getCategoriesList(int $page = 1): array
    {
        $response = Http::withHeader('X-Auth-Token', config('app.crm_api_key'))
            ->get(self::API_URL . '/materials/categories', [
                'page' => $page
            ]);

        return $response->json();
    }

    /**
     * Create crm order
     *
     * @param Order $order
     * @return void
     */
    public function createOrder(Order $order): void
    {
        $deliveryData = [
            [
                'name'  => 'Тип доставки',
                'value' => $order->delivery_type === 'new-post' ? 'Нова Пошта' : 'Самовивіз'
            ]
        ];

        if ($order->delivery_type === 'new-post') {
            $deliveryData[] = [
                'name'  => 'Місто',
                'value' => $order->city_name
            ];
            $deliveryData[] = [
                'name'  => 'Відділення',
                'value' => $order->warehouse_name
            ];
        }

        $payload = [
            'title'             => "Замовлення з сайту #$order->id",
            'total'             => $order->total_price_uah - ($order->discount_uah ?? 0),
            'currency'          => 'UAH',
            'comment'          => $order->comment,
            'products_total_as_total' => false,
            'client_attributes' => [
                'person' => $order->surname . ' ' . $order->name . ' ' . $order->patronymic,
                'email'  => $order->user->email,
                'phones' => [$order->phone]
            ],
            'jobs_attributes'   => $order->products
                ->map(function ($product) {
                    return [
                        'amount'             => $product->quantity,
                        'title'              => $product->product->name,
                        'product_attributes' => [
                            'sku'      => $product->product->sku,
                            'title'    => $product->product->name,
                            'price'    => $product->product->uah_price,
                            'currency' => 'UAH',
                        ]
                    ];
                })
                ->toArray(),
            'discount' => $order->discount_uah,
            'discount_kind' => 'absolute_discount',
            'custom_fields'     => $deliveryData
        ];

        $response = Http::withHeader('X-Auth-Token', config('app.crm_api_key'))
            ->post(self::API_URL . '/agreements', $payload);

        $response->onError(function ($response) use ($order) {
            $order->update([
                'sync_error' => $response->json()
            ]);
        });

        if (!empty($response->json('id'))) {
            $order->update([
                'is_crm_synced' => true,
                'crm_order_id'  => $response->json('id')
            ]);
        }
    }
}
