<?php

namespace App\Services\Crm;

use App\Models\Order\Order;
use App\Notifications\OrderSyncFailedNotification;
use App\Services\Crm\Contracts\CrmServiceInterface;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

/**
 * Class CrmService
 */
class CrmService implements CrmServiceInterface
{
    /**
     * @const
     */
    private const API_URL = 'https://api.keepincrm.com/v1';

    const FEE_PRODUCT_SKU = 'fee';

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
            ->timeout(30)
            ->get(self::API_URL . '/materials', [
                'page' => $page
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
            ->timeout(30)
            ->get(self::API_URL . '/materials/categories', [
                'page' => $page
            ]);

        return $response->json();
    }

    /**
     * @param array $params
     * @param int $page
     * @return array
     */
    public function getOrders(array $params = [], int $page = 1): array
    {
        $response = Http::withHeader('X-Auth-Token', config('app.crm_api_key'))
            ->timeout(30)
            ->get(self::API_URL . '/agreements', array_merge($params, [
                'page' => $page
            ]));

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
        if (app()->isLocal()) {
            $order->update([
                'is_crm_synced' => true,
                'crm_order_id'  => rand(6666, 9999)
            ]);
            return;
        }

        $fee = 0;

        $customFieldsData = [
            [
                'name'  => 'Номер телефону з сайту',
                'value' => $order->phone,
            ],
            [
                'name'  => 'Номер замовлення з сайту',
                'value' => $order->number,
            ],
            [
                'name'  => 'П.І.Б з сайту',
                'value' => $order->full_name,
            ],
            [
                'name'  => 'Тип доставки',
                'value' => $order->delivery_type === 'new-post' ? 'Нова Пошта' : 'Самовивіз'
            ],
            [
                'name'  => 'Гроші з сайту',
                'value' => $order->payment_type_text_for_crm
            ]
        ];

        if ($order->delivery_type === 'new-post') {
            $customFieldsData[] = [
                'name'  => 'Місто',
                'value' => $order->city_name
            ];
            $customFieldsData[] = [
                'name'  => 'Відділення',
                'value' => $order->warehouse_name
            ];
        }

        $totalPrice = 0;

        $products = $order->products
            ->map(function ($product) use (&$totalPrice) {
                $price = $product->product->uah_price;

                $totalPrice += $price * $product->quantity;
                return [
                    'amount'             => $product->quantity,
                    'title'              => $product->product->name,
                    'product_attributes' => [
                        'sku'      => $product->product->sku,
                        'title'    => $product->product->name,
                        'price'    => $price,
                        'currency' => 'UAH',
                    ]
                ];
            })
            ->toArray();

        if ($order->payment_type === Order::PAYMENT_ONLINE) {
            $fee = floor($order->total_price_uah * 0.015);
            $products[] = [
                'amount'             => 1,
                'title'              => 'Пакування',
                'product_attributes' => [
                    'sku'      => self::FEE_PRODUCT_SKU,
                    'title'    => 'Пакування',
                    'price'    => $fee,
                    'currency' => 'UAH',
                ]
            ];
        }

        $payload = [
            'title'                   => "Замовлення з сайту #$order->id",
            'total'                   => $totalPrice + $fee - ($order->discount_uah ?? 0),
            'currency'                => 'UAH',
            'comment'                 => $order->comment,
            'products_total_as_total' => false,
            'client_attributes'       => [
                'person' => $order->surname . ' ' . $order->name . ' ' . $order->patronymic,
                'email'  => $order->user->email,
                'phones' => [$order->phone]
            ],
            'jobs_attributes'         => $products,
            'discount'                => $order->discount_uah,
            'discount_kind'           => 'absolute_discount',
            'custom_fields'           => $customFieldsData
        ];

        $response = Http::withHeader('X-Auth-Token', config('app.crm_api_key'))
            ->timeout(30)
            ->post(self::API_URL . '/agreements', $payload);

        $response->onError(function ($response) use ($order) {
            $order->update([
                'sync_error' => $response->json()
            ]);

            // Send error notification to admin
            Notification::route('telegram', config('services.telegram-bot-api.recipient'))->notify(new OrderSyncFailedNotification($order));
        });

        if (!empty($response->json('id'))) {
            $order->update([
                'is_crm_synced' => true,
                'crm_order_id'  => $response->json('id')
            ]);
        }
    }
}
