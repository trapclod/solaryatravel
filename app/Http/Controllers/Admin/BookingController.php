<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Catamaran;
use App\Models\TimeSlot;
use App\Enums\BookingStatus;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * Display a listing of bookings.
     */
    public function index(Request $request): View
    {
        $query = Booking::with(['catamaran', 'timeSlot', 'payments']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('catamaran')) {
            $query->where('catamaran_id', $request->catamaran);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                    ->orWhere('customer_first_name', 'like', "%{$search}%")
                    ->orWhere('customer_last_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $bookings = $query->orderBy('booking_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $catamarans = Catamaran::orderBy('name')->get();
        $statuses = BookingStatus::cases();

        // Compute stats for the dashboard cards
        $stats = Booking::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('admin.bookings.index', compact('bookings', 'catamarans', 'statuses', 'stats'));
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create(): View
    {
        $catamarans = Catamaran::where('is_active', true)->orderBy('sort_order')->get();
        $timeSlots = TimeSlot::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.bookings.create', compact('catamarans', 'timeSlots'));
    }

    /**
     * Store a newly created booking.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'catamaran_id' => 'required|exists:catamarans,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_type' => 'required|in:seats,exclusive',
            'seats' => 'required_if:booking_type,seats|integer|min:1',
            'customer_first_name' => 'required|string|max:255',
            'customer_last_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'special_requests' => 'nullable|string|max:1000',
            'status' => 'required|in:' . implode(',', array_column(BookingStatus::cases(), 'value')),
            'notes' => 'nullable|string',
        ]);

        try {
            $booking = $this->bookingService->create($validated, 'admin');

            return redirect()
                ->route('admin.bookings.show', $booking)
                ->with('success', 'Prenotazione creata con successo.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking): View
    {
        $booking->load(['catamaran', 'timeSlot', 'addons', 'payments', 'checkIns', 'discountCode']);

        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit(Booking $booking): View
    {
        $catamarans = Catamaran::orderBy('sort_order')->get();
        $timeSlots = TimeSlot::where('is_active', true)->orderBy('sort_order')->get();
        $statuses = BookingStatus::cases();

        return view('admin.bookings.edit', compact('booking', 'catamarans', 'timeSlots', 'statuses'));
    }

    /**
     * Update the specified booking.
     */
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'booking_date' => 'sometimes|date',
            'time_slot_id' => 'sometimes|exists:time_slots,id',
            'seats' => 'sometimes|integer|min:1',
            'customer_first_name' => 'sometimes|string|max:255',
            'customer_last_name' => 'sometimes|string|max:255',
            'customer_email' => 'sometimes|email|max:255',
            'customer_phone' => 'nullable|string|max:30',
            'special_requests' => 'nullable|string|max:1000',
            'status' => 'sometimes|in:' . implode(',', array_column(BookingStatus::cases(), 'value')),
            'admin_notes' => 'nullable|string',
        ]);

        $booking->update($validated);

        return redirect()
            ->route('admin.bookings.show', $booking)
            ->with('success', 'Prenotazione aggiornata con successo.');
    }

    /**
     * Cancel the specified booking.
     */
    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->bookingService->cancel($booking, $request->reason);

            return redirect()
                ->route('admin.bookings.show', $booking)
                ->with('success', 'Prenotazione annullata con successo.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Confirm a pending booking.
     */
    public function confirm(Booking $booking): RedirectResponse
    {
        if ($booking->status !== BookingStatus::PENDING) {
            return redirect()
                ->back()
                ->with('error', 'Solo le prenotazioni in attesa possono essere confermate.');
        }

        $booking->update(['status' => BookingStatus::CONFIRMED]);

        // TODO: Send confirmation email

        return redirect()
            ->back()
            ->with('success', 'Prenotazione confermata con successo.');
    }

    /**
     * Export bookings to CSV.
     */
    public function export(Request $request)
    {
        $query = Booking::with(['catamaran', 'timeSlot']);

        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        $bookings = $query->orderBy('booking_date', 'desc')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="prenotazioni-' . now()->format('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'N. Prenotazione',
                'Data',
                'Orario',
                'Catamarano',
                'Tipo',
                'Posti',
                'Cliente',
                'Email',
                'Telefono',
                'Totale',
                'Stato',
                'Creata il',
            ], ';');

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_number,
                    $booking->booking_date->format('d/m/Y'),
                    $booking->timeSlot->name,
                    $booking->catamaran->name,
                    $booking->isExclusive() ? 'Esclusiva' : 'Posti',
                    $booking->seats,
                    $booking->customer_first_name . ' ' . $booking->customer_last_name,
                    $booking->customer_email,
                    $booking->customer_phone,
                    number_format($booking->total_amount, 2, ',', '.') . ' €',
                    $booking->status->value,
                    $booking->created_at->format('d/m/Y H:i'),
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get calendar data for bookings.
     */
    public function calendar(Request $request): View
    {
        $month = $request->get('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        $bookings = Booking::whereBetween('booking_date', [$startDate, $endDate])
            ->with(['catamaran', 'timeSlot'])
            ->orderBy('booking_date')
            ->get()
            ->groupBy(fn($b) => $b->booking_date->format('Y-m-d'));

        return view('admin.bookings.calendar', compact('bookings', 'month', 'startDate', 'endDate'));
    }
}
