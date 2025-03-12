<?php

namespace App\Mail;

use App\Models\Order\Order;
use App\Models\ProductSubscription\ProductSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductAvailableMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public ProductSubscription $productSubscription;


    /**
     * Create a new message instance.
     * @param ProductSubscription $productSubscription
     */
    public function __construct(ProductSubscription $productSubscription)
    {
        $this->productSubscription = $productSubscription;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->productSubscription->product->name . ' знову в наявності'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.product-available',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
