<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index(Request $request): View
    {
        $query = Payment::with(['booking.catamaran', 'booking.user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by gateway
        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('gateway_payment_id', 'like', "%{$search}%")
                  ->orWhere('gateway_transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('booking', function ($q) use ($search) {
                      $q->where('booking_number', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->latest()->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'total' => Payment::count(),
            'succeeded' => Payment::where('status', PaymentStatus::SUCCEEDED)->count(),
            'pending' => Payment::where('status', PaymentStatus::PENDING)->count(),
            'failed' => Payment::where('status', PaymentStatus::FAILED)->count(),
            'refunded' => Payment::whereIn('status', [PaymentStatus::REFUNDED, PaymentStatus::PARTIALLY_REFUNDED])->count(),
            'total_amount' => Payment::where('status', PaymentStatus::SUCCEEDED)->sum('amount'),
            'refunded_amount' => Payment::sum('refunded_amount'),
        ];

        $statuses = PaymentStatus::cases();

        return view('admin.payments.index', compact('payments', 'stats', 'statuses'));
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment): View
    {
        $payment->load(['booking.catamaran', 'booking.user', 'booking.timeSlot']);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Process a refund for the payment.
     */
    public function refund(Request $request, Payment $payment): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . ($payment->amount - $payment->refunded_amount),
            'reason' => 'required|string|max:500',
        ]);

        // Check if payment can be refunded
        if (!in_array($payment->status, [PaymentStatus::SUCCEEDED, PaymentStatus::PARTIALLY_REFUNDED])) {
            return back()->with('error', 'Questo pagamento non può essere rimborsato.');
        }

        $refundAmount = (float) $request->amount;
        $previousRefunded = (float) $payment->refunded_amount;
        $totalRefunded = $previousRefunded + $refundAmount;

        // Determine new status
        if ($totalRefunded >= $payment->amount) {
            $newStatus = PaymentStatus::REFUNDED;
        } else {
            $newStatus = PaymentStatus::PARTIALLY_REFUNDED;
        }

        // In a real app, you would call Stripe API here
        // \Stripe\Refund::create([...])

        $payment->update([
            'status' => $newStatus,
            'refunded_amount' => $totalRefunded,
            'refund_reason' => $request->reason,
            'refunded_at' => now(),
        ]);

        // Update booking status if fully refunded
        if ($newStatus === PaymentStatus::REFUNDED && $payment->booking) {
            $payment->booking->update([
                'status' => 'refunded',
                'cancelled_at' => now(),
                'cancellation_reason' => 'Rimborso processato: ' . $request->reason,
            ]);
        }

        return back()->with('success', 'Rimborso di €' . number_format($refundAmount, 2, ',', '.') . ' processato con successo.');
    }
}
