<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Webhook;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {
        Stripe::setApiKey(config('payment.stripe.secret_key'));
    }

    /**
     * Show the payment page for a booking.
     */
    public function show(string $bookingNumber): View|RedirectResponse
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['catamaran', 'timeSlot', 'addons'])
            ->firstOrFail();

        // Check if booking is still payable
        if (!$booking->isPending()) {
            return redirect()
                ->route('bookings.show', $bookingNumber)
                ->with('info', 'Questa prenotazione è già stata pagata o non è più valida.');
        }

        // Check if payment deadline has passed
        if ($booking->payment_deadline && now()->gt($booking->payment_deadline)) {
            $booking->update(['status' => BookingStatus::EXPIRED]);
            return redirect()
                ->route('bookings.create')
                ->with('error', 'Il tempo per completare il pagamento è scaduto. Effettua una nuova prenotazione.');
        }

        return view('payments.show', compact('booking'));
    }

    /**
     * Create a Stripe Checkout session.
     */
    public function createCheckoutSession(string $bookingNumber): RedirectResponse
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['catamaran', 'timeSlot', 'addons'])
            ->firstOrFail();

        if (!$booking->isPending()) {
            return redirect()
                ->route('bookings.show', $bookingNumber)
                ->with('error', 'Questa prenotazione non può essere pagata.');
        }

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Escursione ' . $booking->catamaran->name,
                            'description' => sprintf(
                                '%s - %s (%s)',
                                $booking->booking_date->format('d/m/Y'),
                                $booking->timeSlot->name,
                                $booking->isExclusive() ? 'Esclusiva' : $booking->seats . ' posti'
                            ),
                        ],
                        'unit_amount' => (int) ($booking->total_amount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success', $bookingNumber) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel', $bookingNumber),
                'customer_email' => $booking->customer_email,
                'metadata' => [
                    'booking_number' => $booking->booking_number,
                    'booking_id' => $booking->id,
                ],
                'expires_at' => now()->addMinutes(30)->timestamp,
            ]);

            // Create payment record
            Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_amount,
                'currency' => 'EUR',
                'payment_method' => 'stripe',
                'payment_gateway' => 'stripe',
                'transaction_id' => $session->id,
                'status' => PaymentStatus::PENDING,
                'metadata' => ['session_id' => $session->id],
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            report($e);
            return redirect()
                ->back()
                ->with('error', 'Si è verificato un errore durante la creazione del pagamento. Riprova.');
        }
    }

    /**
     * Handle successful payment return.
     */
    public function success(Request $request, string $bookingNumber): View|RedirectResponse
    {
        $booking = Booking::where('booking_number', $bookingNumber)->firstOrFail();

        $sessionId = $request->get('session_id');

        if ($sessionId) {
            try {
                $session = Session::retrieve($sessionId);

                if ($session->payment_status === 'paid') {
                    // Update payment record
                    $payment = Payment::where('transaction_id', $sessionId)->first();
                    if ($payment) {
                        $payment->update([
                            'status' => PaymentStatus::COMPLETED,
                            'paid_at' => now(),
                            'metadata' => array_merge(
                                $payment->metadata ?? [],
                                ['payment_intent' => $session->payment_intent]
                            ),
                        ]);
                    }

                    // Update booking status
                    $booking->update(['status' => BookingStatus::CONFIRMED]);

                    // TODO: Send confirmation email

                    return view('payments.success', compact('booking'));
                }
            } catch (\Exception $e) {
                report($e);
            }
        }

        // If we can't verify payment, redirect to booking page
        return redirect()
            ->route('bookings.show', $bookingNumber)
            ->with('info', 'Stiamo verificando il tuo pagamento. Riceverai una conferma via email.');
    }

    /**
     * Handle cancelled payment.
     */
    public function cancel(string $bookingNumber): View
    {
        $booking = Booking::where('booking_number', $bookingNumber)->firstOrFail();

        return view('payments.cancel', compact('booking'));
    }

    /**
     * Handle Stripe webhooks.
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('payment.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Webhook verification failed'], 400);
        }

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleCheckoutCompleted($session);
                break;

            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentSucceeded($paymentIntent);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentFailed($paymentIntent);
                break;

            case 'charge.refunded':
                $charge = $event->data->object;
                $this->handleRefund($charge);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle checkout session completed.
     */
    protected function handleCheckoutCompleted($session): void
    {
        $payment = Payment::where('transaction_id', $session->id)->first();

        if ($payment && $session->payment_status === 'paid') {
            $payment->update([
                'status' => PaymentStatus::COMPLETED,
                'paid_at' => now(),
                'metadata' => array_merge(
                    $payment->metadata ?? [],
                    ['payment_intent' => $session->payment_intent]
                ),
            ]);

            $payment->booking->update(['status' => BookingStatus::CONFIRMED]);

            // TODO: Send confirmation email
        }
    }

    /**
     * Handle successful payment intent.
     */
    protected function handlePaymentSucceeded($paymentIntent): void
    {
        $payment = Payment::whereJsonContains('metadata->payment_intent', $paymentIntent->id)->first();

        if ($payment && $payment->status !== PaymentStatus::COMPLETED) {
            $payment->update([
                'status' => PaymentStatus::COMPLETED,
                'paid_at' => now(),
            ]);

            $payment->booking->update(['status' => BookingStatus::CONFIRMED]);
        }
    }

    /**
     * Handle failed payment.
     */
    protected function handlePaymentFailed($paymentIntent): void
    {
        $payment = Payment::whereJsonContains('metadata->payment_intent', $paymentIntent->id)->first();

        if ($payment) {
            $payment->update([
                'status' => PaymentStatus::FAILED,
                'metadata' => array_merge(
                    $payment->metadata ?? [],
                    ['failure_message' => $paymentIntent->last_payment_error?->message]
                ),
            ]);
        }
    }

    /**
     * Handle refund.
     */
    protected function handleRefund($charge): void
    {
        $payment = Payment::whereJsonContains('metadata->payment_intent', $charge->payment_intent)->first();

        if ($payment) {
            $refundedAmount = $charge->amount_refunded / 100;

            if ($charge->refunded) {
                $payment->update([
                    'status' => PaymentStatus::REFUNDED,
                    'refunded_amount' => $refundedAmount,
                    'refunded_at' => now(),
                ]);
            } else {
                $payment->update([
                    'status' => PaymentStatus::PARTIALLY_REFUNDED,
                    'refunded_amount' => $refundedAmount,
                ]);
            }
        }
    }
}
