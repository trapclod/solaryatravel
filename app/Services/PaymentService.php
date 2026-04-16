<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Refund;
use Stripe\PaymentIntent;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('payment.stripe.secret_key'));
    }

    /**
     * Create a Stripe checkout session for a booking.
     */
    public function createCheckoutSession(Booking $booking): array
    {
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $this->buildLineItems($booking),
            'mode' => 'payment',
            'success_url' => route('payment.success', $booking->booking_number) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancel', $booking->booking_number),
            'customer_email' => $booking->customer_email,
            'metadata' => [
                'booking_number' => $booking->booking_number,
                'booking_id' => $booking->id,
            ],
            'expires_at' => now()->addMinutes(30)->timestamp,
            'locale' => $booking->locale ?? 'it',
        ]);

        // Create payment record
        Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_amount,
            'currency' => 'EUR',
            'payment_method' => 'card',
            'payment_gateway' => 'stripe',
            'transaction_id' => $session->id,
            'status' => PaymentStatus::PENDING,
            'metadata' => ['session_id' => $session->id],
        ]);

        return [
            'session_id' => $session->id,
            'url' => $session->url,
        ];
    }

    /**
     * Build line items for Stripe checkout.
     */
    protected function buildLineItems(Booking $booking): array
    {
        $items = [];

        // Main booking item
        $description = sprintf(
            '%s - %s (%s)',
            $booking->booking_date->format('d/m/Y'),
            $booking->timeSlot->name,
            $booking->isExclusive() ? 'Esclusiva' : $booking->seats . ' posti'
        );

        $items[] = [
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                    'name' => 'Escursione ' . $booking->catamaran->name,
                    'description' => $description,
                ],
                'unit_amount' => (int) ($booking->base_price * 100),
            ],
            'quantity' => 1,
        ];

        // Add addons
        foreach ($booking->addons as $addon) {
            $items[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $addon->name,
                    ],
                    'unit_amount' => (int) ($addon->pivot->total_price * 100),
                ],
                'quantity' => 1,
            ];
        }

        return $items;
    }

    /**
     * Verify a checkout session and complete payment.
     */
    public function verifyCheckoutSession(string $sessionId): array
    {
        try {
            $session = StripeSession::retrieve($sessionId);

            if ($session->payment_status !== 'paid') {
                return [
                    'success' => false,
                    'message' => 'Il pagamento non è stato completato.',
                ];
            }

            $payment = Payment::where('transaction_id', $sessionId)->first();

            if (!$payment) {
                return [
                    'success' => false,
                    'message' => 'Pagamento non trovato.',
                ];
            }

            // Update payment record
            $payment->update([
                'status' => PaymentStatus::COMPLETED,
                'paid_at' => now(),
                'metadata' => array_merge(
                    $payment->metadata ?? [],
                    ['payment_intent' => $session->payment_intent]
                ),
            ]);

            // Update booking status
            $payment->booking->update(['status' => BookingStatus::CONFIRMED]);

            return [
                'success' => true,
                'payment' => $payment,
                'booking' => $payment->booking,
            ];
        } catch (\Exception $e) {
            report($e);
            return [
                'success' => false,
                'message' => 'Errore durante la verifica del pagamento.',
            ];
        }
    }

    /**
     * Process a refund for a booking.
     */
    public function refund(Booking $booking, ?float $amount = null): array
    {
        $payment = $booking->payments()
            ->where('status', PaymentStatus::COMPLETED)
            ->latest('paid_at')
            ->first();

        if (!$payment) {
            return [
                'success' => false,
                'message' => 'Nessun pagamento da rimborsare.',
            ];
        }

        try {
            $paymentIntent = $payment->metadata['payment_intent'] ?? null;

            if (!$paymentIntent) {
                return [
                    'success' => false,
                    'message' => 'ID pagamento non trovato.',
                ];
            }

            $refundParams = [
                'payment_intent' => $paymentIntent,
            ];

            // If partial refund
            if ($amount && $amount < $payment->amount) {
                $refundParams['amount'] = (int) ($amount * 100);
            }

            $refund = Refund::create($refundParams);

            $refundedAmount = $refund->amount / 100;
            $isFullRefund = $refundedAmount >= $payment->amount;

            $payment->update([
                'status' => $isFullRefund ? PaymentStatus::REFUNDED : PaymentStatus::PARTIALLY_REFUNDED,
                'refunded_amount' => $refundedAmount,
                'refunded_at' => now(),
                'metadata' => array_merge(
                    $payment->metadata ?? [],
                    ['refund_id' => $refund->id]
                ),
            ]);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'amount' => $refundedAmount,
                'status' => $refund->status,
            ];
        } catch (\Exception $e) {
            report($e);
            return [
                'success' => false,
                'message' => 'Errore durante il rimborso: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Calculate refund amount based on cancellation policy.
     */
    public function calculateRefundAmount(Booking $booking): array
    {
        $payment = $booking->payments()
            ->where('status', PaymentStatus::COMPLETED)
            ->latest('paid_at')
            ->first();

        if (!$payment) {
            return [
                'refundable' => false,
                'amount' => 0,
                'percentage' => 0,
                'reason' => 'Nessun pagamento trovato.',
            ];
        }

        $hoursUntilBooking = now()->diffInHours($booking->booking_date, false);

        // Get cancellation policy from config
        $policies = config('booking.cancellation_policies', [
            ['hours' => 48, 'refund_percentage' => 100],
            ['hours' => 24, 'refund_percentage' => 50],
            ['hours' => 0, 'refund_percentage' => 0],
        ]);

        $refundPercentage = 0;
        $reason = '';

        foreach ($policies as $policy) {
            if ($hoursUntilBooking >= $policy['hours']) {
                $refundPercentage = $policy['refund_percentage'];
                if ($policy['hours'] > 0) {
                    $reason = "Cancellazione con più di {$policy['hours']} ore di anticipo";
                } else {
                    $reason = 'Cancellazione tardiva';
                }
                break;
            }
        }

        $refundAmount = ($payment->amount * $refundPercentage) / 100;

        return [
            'refundable' => $refundPercentage > 0,
            'amount' => round($refundAmount, 2),
            'percentage' => $refundPercentage,
            'reason' => $reason,
            'original_amount' => $payment->amount,
        ];
    }

    /**
     * Get payment status from Stripe.
     */
    public function getPaymentStatus(string $paymentIntentId): array
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            return [
                'success' => true,
                'status' => $paymentIntent->status,
                'amount' => $paymentIntent->amount / 100,
                'currency' => strtoupper($paymentIntent->currency),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
