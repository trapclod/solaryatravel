<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Catamaran;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * Show the booking wizard for a specific catamaran.
     */
    public function create(?string $slug = null): View
    {
        if ($slug) {
            $catamaran = Catamaran::where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail();

            return view('bookings.create', compact('catamaran'));
        }

        return view('bookings.create');
    }

    /**
     * Show the booking confirmation page.
     */
    public function confirmation(string $bookingNumber): View
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['catamaran', 'timeSlot', 'addons'])
            ->firstOrFail();

        return view('bookings.confirmation', compact('booking'));
    }

    /**
     * Show booking details for authenticated users.
     */
    public function show(string $bookingNumber): View
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['catamaran', 'timeSlot', 'addons', 'payments', 'checkIns'])
            ->firstOrFail();

        // Check if user is authorized to view this booking
        if (auth()->check()) {
            if (auth()->user()->role !== 'admin' && 
                $booking->user_id !== auth()->id() &&
                $booking->customer_email !== auth()->user()->email) {
                abort(403);
            }
        } else {
            // If not logged in, check session
            $accessToken = session('booking_access_' . $bookingNumber);
            if (!$accessToken || $accessToken !== $booking->access_token) {
                return redirect()->route('bookings.verify', $bookingNumber);
            }
        }

        return view('bookings.show', compact('booking'));
    }

    /**
     * Show the booking verification form (for guests).
     */
    public function verify(string $bookingNumber): View
    {
        $booking = Booking::where('booking_number', $bookingNumber)->firstOrFail();

        return view('bookings.verify', compact('booking'));
    }

    /**
     * Verify booking access via email.
     */
    public function verifyEmail(Request $request, string $bookingNumber): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $booking = Booking::where('booking_number', $bookingNumber)
            ->where('customer_email', $request->email)
            ->firstOrFail();

        // Generate access token and store in session
        $accessToken = hash('sha256', $booking->id . $booking->customer_email . now()->timestamp);
        $booking->update(['access_token' => $accessToken]);
        session(['booking_access_' . $bookingNumber => $accessToken]);

        return redirect()->route('bookings.show', $bookingNumber);
    }

    /**
     * Cancel a booking.
     */
    public function cancel(string $bookingNumber): RedirectResponse
    {
        $booking = Booking::where('booking_number', $bookingNumber)->firstOrFail();

        // Check authorization
        if (auth()->check() && auth()->user()->role !== 'admin') {
            if ($booking->user_id !== auth()->id() && 
                $booking->customer_email !== auth()->user()->email) {
                abort(403);
            }
        }

        try {
            $this->bookingService->cancel($booking, 'Annullata dal cliente');
            
            return redirect()
                ->route('bookings.show', $bookingNumber)
                ->with('success', 'La prenotazione è stata annullata con successo.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Request booking modification.
     */
    public function requestModification(Request $request, string $bookingNumber): RedirectResponse
    {
        $booking = Booking::where('booking_number', $bookingNumber)->firstOrFail();

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // TODO: Send modification request notification to admin

        return redirect()
            ->back()
            ->with('success', 'La tua richiesta di modifica è stata inviata. Ti contatteremo presto.');
    }
}
