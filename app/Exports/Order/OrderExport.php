<?php

namespace App\Exports\Order;

use App\Models\Order\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrderExport implements FromArray, ShouldAutoSize
{
    /**
     * @var Order
     */
    private Order $order;

    /**
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function array(): array
    {
        $orderData[] = ['Номер', $this->order->number];

        if (!empty($this->order->ttn)) {
            $orderData[] = ['ТТН', "{$this->order->ttn} ({$this->order->nova_poshta_status})"];
        }

        $orderData[] = ['ПІБ', $this->order->full_name];
        $orderData[] = ['Телефон', $this->order->phone];
        $orderData[] = ['Спосіб доставки', $this->order->delivery_type_text];

        if (!empty($this->order->city_name)) {
            $orderData[] = ['Місто', $this->order->city_name];
        }

        if (!empty($this->order->warehouse_name)) {
            $orderData[] = ['Відділення', $this->order->warehouse_name];
        }

        $orderData[] = ['Спосіб оплати', $this->order->payment_type_text];
        $orderData[] = ['Вартість $', $this->order->total_price_usd];
        $orderData[] = ['Вартість грн', $this->order->total_price_uah];

        if ($this->order->discount_percent > 0) {
            $orderData[] = ["Знижка ({$this->order->discount_percent}%), $", $this->order->discount_usd];
            $orderData[] = ["Знижка ({$this->order->discount_percent}%), грн.", $this->order->discount_uah];

            $discountedPriceUsd = round($this->order->total_price_usd - $this->order->discount_usd, 2);
            $discountedPriceUah = round($this->order->total_price_uah - $this->order->discount_uah, 2);

            $orderData[] = ['Вартість зі знижкою, $', $discountedPriceUsd];
            $orderData[] = ['Вартість зі знижкою, грн.', $discountedPriceUah];
        }

        $productRows = [];
        foreach ($this->order->products as $orderProduct) {
            $productRows[] = [
                $orderProduct->product->name,
                $orderProduct->product->sku,
                $orderProduct->product->price,
                $orderProduct->quantity,
                $orderProduct->total_price,
                $orderProduct->total_price_uah,
            ];
        }

        return [
            $orderData,
            [''],
            ['Товари:'],
            ['Назва', 'Арт.', 'Ціна за шт, $', 'Кількість', 'Вартість, $', 'Вартість, грн'],
            $productRows
        ];
    }
}
