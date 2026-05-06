<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Catamaran;
use App\Models\Tour;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    public function index(Request $request): View
    {
        $query = Booking::with(['tour', 'departure', 'payments']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tour')) {
            $query->where('tour_id', $request->tour);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('booking_number', 'like', "%{$s}%")
                    ->orWhere('customer_first_name', 'like', "%{$s}%")
                    ->orWhere('customer_last_name', 'like', "%{$s}%")
                    ->orWhere('customer_email', 'like', "%{$s}%");
            });
        }

        $bookings = $query->orderBy('booking_date', 'desc')->paginate(20)->withQueryString();
        $tours = Tour::orderBy('name')->get();
        $statuses = BookingStatus::cases();
        $stats = Booking::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')->pluck('count', 'status')->toArray();

        return view('admin.bookings.index', compact('bookings', 'tours', 'statuses', 'stats'));
    }

    public function show(Booking $booking): View
    {
        $booking->load([
            'tour',
            'departure',
            'addons.addon',
            'payments',
            'checkIns',
            'discountCode',
            'seatRecords.catamaran',
            'seatRecords.ageBracket',
        ]);
        $catamarans = Catamaran::active()->ordered()->get();
        return view('admin.bookings.show', compact('booking', 'catamarans'));
    }

    public function edit(Booking $booking): View
    {
        $booking->load(['tour', 'departure', 'seatRecords.catamaran', 'seatRecords.ageBracket']);
        $catamarans = Catamaran::active()->ordered()->get();
        $statuses = BookingStatus::cases();
        return view('admin.bookings.edit', compact('booking', 'catamarans', 'statuses'));
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:' . implode(',', array_column(BookingStatus::cases(), 'value')),
            'special_requests' => 'nullable|string|max:1000',
            'customer_phone' => 'nullable|string|max:30',
        ]);
        $booking->update($validated);
        return redirect()->route('admin.bookings.show', $booking)->with('success', 'Prenotazione aggiornata.');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Prenotazione eliminata.');
    }

    public function confirm(Booking $booking): RedirectResponse
    {
        if ($booking->status !== BookingStatus::PENDING) {
            return back()->with('error', 'Solo le prenotazioni in attesa possono essere confermate.');
        }
        $booking->update(['status' => BookingStatus::CONFIRMED, 'confirmed_at' => now()]);
        return back()->with('success', 'Prenotazione confermata.');
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate(['reason' => 'required|string|max:500']);
        try {
            $this->bookingService->cancel($booking, $request->reason);
            return back()->with('success', 'Prenotazione annullata.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function refund(Booking $booking): RedirectResponse
    {
        $booking->update(['status' => 'refunded']);
        return back()->with('success', 'Prenotazione marcata come rimborsata.');
    }

    public function resendConfirmation(Booking $booking): RedirectResponse
    {
        // TODO: integrazione email
        return back()->with('success', 'Conferma reinviata.');
    }

    public function export(Request $request)
    {
        return back()->with('info', 'Export non ancora implementato.');
    }

    /**
     * Sposta un singolo posto su un altro catamarano (richiesta AJAX o form).
     */
    public function moveSeat(Request $request, Booking $booking, BookingSeat $seat): RedirectResponse
    {
        if ($seat->booking_id !== $booking->id) {
            abort(404);
        }
        $request->validate(['catamaran_id' => 'required|exists:catamarans,id']);
        try {
            $this->bookingService->moveSeat($seat, (int) $request->catamaran_id);
            return back()->with('success', 'Posto spostato.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
