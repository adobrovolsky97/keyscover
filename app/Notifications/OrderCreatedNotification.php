<?php

namespace App\Notifications;

use App\Models\Order\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use JsonException;
use NotificationChannels\Telegram\TelegramMessage;

class OrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private Order $order;

    /**
     * Create a new notification instance.
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['telegram'];
    }

    /**
     * Get the mail representation of the notification.
     * @throws JsonException
     */
    public function toTelegram(object $notifiable): TelegramMessage
    {
        return TelegramMessage::create()
            ->to($notifiable->routeNotificationFor('telegram'))
            ->line("Клієнт: *{$this->order->full_name}*")
            ->line("Вартість: *{$this->order->total_price_usd} $ / {$this->order->total_price_uah} грн*")
            ->line("Доставка: *{$this->order->delivery_type_text}*")
            ->line("Оплата: *{$this->order->payment_type_text}*")
            ->lineIf(!empty($this->order->comment), "Коментар: *{$this->order->comment}*")
            ->line("Замовлення: *{$this->order->number}*");

    }
}
