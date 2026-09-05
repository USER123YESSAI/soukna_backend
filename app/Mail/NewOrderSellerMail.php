<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrderSellerMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order, public User $seller)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle commande reçue #' . $this->order->order_number . ' — Soukna',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new_order_seller',
        );
    }
}
