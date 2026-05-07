<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingTickets extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'I tuoi biglietti · Prenotazione #' . $this->booking->booking_number,
        );
    }

    public function content(): Content
    {
        $this->booking->loadMissing(['tour', 'departure', 'seatRecords.ageBracket', 'seatRecords.catamaran']);

        // Pre-generate QR PNG bytes per ogni seat (per essere embeddabili nel template)
        $tickets = $this->booking->seatRecords->map(function ($seat) {
            $png = QrCode::format('png')
                ->size(280)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($seat->qr_code);
            return [
                'seat' => $seat,
                'qr_data' => 'data:image/png;base64,' . base64_encode($png),
            ];
        });

        return new Content(
            view: 'emails.bookings.tickets',
            with: [
                'booking' => $this->booking,
                'tickets' => $tickets,
            ],
        );
    }
}
