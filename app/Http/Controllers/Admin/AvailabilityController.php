<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Catamaran;
use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AvailabilityController extends Controller
{
    /**
     * Display listing of catamarans for availability management.
     */
    public function index(): View
    {
        $catamarans = Catamaran::withCount(['bookings' => function ($query) {
            $query->where('booking_date', '>=', now())
                  ->where('status', '!=', 'cancelled');
        }])
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

        $timeSlots = TimeSlot::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.availability.index', compact('catamarans', 'timeSlots'));
    }

    /**
     * Show availability calendar for a specific catamaran.
     */
    public function calendar(Catamaran $catamaran): View
    {
        $timeSlots = TimeSlot::where('is_active', true)->orderBy('sort_order')->get();
        
        // Get availability for the next 3 months
        $startDate = now()->startOfMonth();
        $endDate = now()->addMonths(3)->endOfMonth();
        
        $availability = Availability::where('catamaran_id', $catamaran->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('timeSlot')
            ->get()
            ->groupBy(function ($item) {
                return $item->date->format('Y-m-d');
            });

        // Get bookings for the period
        $bookings = $catamaran->bookings()
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->whereNotIn('status', ['cancelled'])
            ->with('timeSlot')
            ->get()
            ->groupBy(function ($item) {
                return $item->booking_date->format('Y-m-d');
            });

        return view('admin.availability.calendar', compact('catamaran', 'timeSlots', 'availability', 'bookings', 'startDate', 'endDate'));
    }

    /**
     * Update availability for a specific date and time slot.
     */
    public function update(Request $request, Catamaran $catamaran): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time_slot_id' => 'required|exists:time_slots,id',
            'seats_available' => 'required|integer|min:0|max:' . $catamaran->capacity,
            'custom_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:available,blocked,fully_booked',
        ]);

        $availability = Availability::updateOrCreate(
            [
                'catamaran_id' => $catamaran->id,
                'date' => $validated['date'],
                'time_slot_id' => $validated['time_slot_id'],
            ],
            [
                'seats_available' => $validated['seats_available'],
                'custom_price' => $validated['custom_price'],
                'status' => $validated['status'],
            ]
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'availability' => $availability]);
        }

        return back()->with('success', 'Disponibilità aggiornata con successo.');
    }

    /**
     * Block a date for a catamaran.
     */
    public function block(Request $request, Catamaran $catamaran): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time_slot_id' => 'nullable|exists:time_slots,id',
            'block_reason' => 'nullable|string|max:255',
        ]);

        // Check for existing bookings
        $existingBookings = $catamaran->bookings()
            ->where('booking_date', $validated['date'])
            ->when($validated['time_slot_id'], function ($query, $timeSlotId) {
                $query->where('time_slot_id', $timeSlotId);
            })
            ->whereNotIn('status', ['cancelled'])
            ->count();

        if ($existingBookings > 0) {
            $message = "Impossibile bloccare: ci sono {$existingBookings} prenotazioni attive per questa data.";
            
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            
            return back()->with('error', $message);
        }

        // Block all time slots or specific one
        if ($validated['time_slot_id']) {
            Availability::updateOrCreate(
                [
                    'catamaran_id' => $catamaran->id,
                    'date' => $validated['date'],
                    'time_slot_id' => $validated['time_slot_id'],
                ],
                [
                    'status' => 'blocked',
                    'block_reason' => $validated['block_reason'] ?? 'Bloccato manualmente',
                    'seats_available' => 0,
                ]
            );
        } else {
            // Block all time slots for the date
            $timeSlots = TimeSlot::where('is_active', true)->get();
            foreach ($timeSlots as $slot) {
                Availability::updateOrCreate(
                    [
                        'catamaran_id' => $catamaran->id,
                        'date' => $validated['date'],
                        'time_slot_id' => $slot->id,
                    ],
                    [
                        'status' => 'blocked',
                        'block_reason' => $validated['block_reason'] ?? 'Bloccato manualmente',
                        'seats_available' => 0,
                    ]
                );
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Data bloccata con successo.');
    }

    /**
     * Unblock a date for a catamaran.
     */
    public function unblock(Request $request, Catamaran $catamaran): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time_slot_id' => 'nullable|exists:time_slots,id',
        ]);

        $query = Availability::where('catamaran_id', $catamaran->id)
            ->where('date', $validated['date'])
            ->where('status', 'blocked');

        if ($validated['time_slot_id']) {
            $query->where('time_slot_id', $validated['time_slot_id']);
        }

        $query->update([
            'status' => 'available',
            'block_reason' => null,
            'seats_available' => $catamaran->capacity,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Data sbloccata con successo.');
    }

    /**
     * Bulk update availability for multiple dates.
     */
    public function bulkUpdate(Request $request, Catamaran $catamaran): RedirectResponse
    {
        $validated = $request->validate([
            'date_from' => 'required|date|after_or_equal:today',
            'date_to' => 'required|date|after_or_equal:date_from',
            'time_slot_id' => 'nullable|exists:time_slots,id',
            'action' => 'required|in:block,unblock,set_seats',
            'seats_available' => 'required_if:action,set_seats|integer|min:0|max:' . $catamaran->capacity,
            'block_reason' => 'nullable|string|max:255',
            'days_of_week' => 'nullable|array',
            'days_of_week.*' => 'integer|min:0|max:6',
        ]);

        $period = CarbonPeriod::create($validated['date_from'], $validated['date_to']);
        $timeSlots = $validated['time_slot_id'] 
            ? [TimeSlot::find($validated['time_slot_id'])] 
            : TimeSlot::where('is_active', true)->get()->all();

        $daysOfWeek = $validated['days_of_week'] ?? [0, 1, 2, 3, 4, 5, 6];
        $updatedCount = 0;

        foreach ($period as $date) {
            // Skip if day of week not selected
            if (!in_array($date->dayOfWeek, $daysOfWeek)) {
                continue;
            }

            foreach ($timeSlots as $slot) {
                // Check for bookings before blocking
                if ($validated['action'] === 'block') {
                    $hasBookings = $catamaran->bookings()
                        ->where('booking_date', $date)
                        ->where('time_slot_id', $slot->id)
                        ->whereNotIn('status', ['cancelled'])
                        ->exists();

                    if ($hasBookings) {
                        continue;
                    }
                }

                $data = match ($validated['action']) {
                    'block' => [
                        'status' => 'blocked',
                        'block_reason' => $validated['block_reason'] ?? 'Bloccato in blocco',
                        'seats_available' => 0,
                    ],
                    'unblock' => [
                        'status' => 'available',
                        'block_reason' => null,
                        'seats_available' => $catamaran->capacity,
                    ],
                    'set_seats' => [
                        'seats_available' => $validated['seats_available'],
                    ],
                };

                Availability::updateOrCreate(
                    [
                        'catamaran_id' => $catamaran->id,
                        'date' => $date->format('Y-m-d'),
                        'time_slot_id' => $slot->id,
                    ],
                    $data
                );

                $updatedCount++;
            }
        }

        return back()->with('success', "Aggiornate {$updatedCount} disponibilità con successo.");
    }

    /**
     * Get availability data as JSON for calendar.
     */
    public function getCalendarData(Request $request, Catamaran $catamaran): JsonResponse
    {
        $startDate = Carbon::parse($request->start);
        $endDate = Carbon::parse($request->end);

        $availability = Availability::where('catamaran_id', $catamaran->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('timeSlot')
            ->get();

        $bookings = $catamaran->bookings()
            ->whereBetween('booking_date', [$startDate, $endDate])
            ->whereNotIn('status', ['cancelled'])
            ->with('timeSlot')
            ->get();

        $events = [];

        // Add availability events
        foreach ($availability as $avail) {
            $color = match ($avail->status) {
                'blocked' => '#EF4444',
                'fully_booked' => '#F59E0B',
                'available' => '#10B981',
                default => '#6B7280',
            };

            $events[] = [
                'id' => 'avail-' . $avail->id,
                'title' => $avail->status === 'blocked' 
                    ? 'Bloccato' 
                    : "Posti: {$avail->seats_available}",
                'start' => $avail->date->format('Y-m-d'),
                'color' => $color,
                'extendedProps' => [
                    'type' => 'availability',
                    'status' => $avail->status,
                    'seats_available' => $avail->seats_available,
                    'time_slot' => $avail->timeSlot?->name,
                ],
            ];
        }

        // Add booking events
        foreach ($bookings as $booking) {
            $events[] = [
                'id' => 'booking-' . $booking->id,
                'title' => "#{$booking->booking_number} - {$booking->seats} posti",
                'start' => $booking->booking_date->format('Y-m-d'),
                'color' => '#3B82F6',
                'extendedProps' => [
                    'type' => 'booking',
                    'booking_number' => $booking->booking_number,
                    'customer' => $booking->customer_first_name . ' ' . $booking->customer_last_name,
                    'seats' => $booking->seats,
                    'status' => $booking->status,
                ],
            ];
        }

        return response()->json($events);
    }
}
