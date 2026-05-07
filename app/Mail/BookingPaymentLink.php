<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingPaymentLink extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $checkoutUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Completa il pagamento per la tua prenotazione #' . $this->booking->booking_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bookings.payment-link',
            with: [
                'booking' => $this->booking,
                'checkoutUrl' => $this->checkoutUrl,
            ],
        );
    }
}
