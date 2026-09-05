<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public string $status)
    {
    }

    public function envelope(): Envelope
    {
        $statusLabels = [
            'confirmed' => 'confirmée',
            'shipped' => 'expédiée',
            'delivered' => 'livrée',
            'cancelled' => 'annulée',
        ];
        $label = $statusLabels[$this->status] ?? $this->status;

        return new Envelope(
            subject: "Mise à jour : Votre commande #{$this->order->order_number} est {$label} — Soukna",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order_status_updated',
        );
    }
}
