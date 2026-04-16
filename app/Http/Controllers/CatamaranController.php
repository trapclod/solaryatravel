<?php

namespace App\Http\Controllers;

use App\Models\Catamaran;
use App\Models\Availability;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatamaranController extends Controller
{
    /**
     * Display a listing of catamarans.
     */
    public function index(): View
    {
        $catamarans = Catamaran::where('is_active', true)
            ->orderBy('sort_order')
            ->with(['images', 'addons'])
            ->get();

        return view('catamarans.index', compact('catamarans'));
    }

    /**
     * Display the specified catamaran.
     */
    public function show(string $slug): View
    {
        $catamaran = Catamaran::where('slug', $slug)
            ->where('is_active', true)
            ->with(['images', 'addons'])
            ->firstOrFail();

        // Get availability for the next 3 months
        $availableDates = Availability::where('catamaran_id', $catamaran->id)
            ->where('date', '>=', now()->toDateString())
            ->where('date', '<=', now()->addMonths(3)->toDateString())
            ->where('status', '!=', 'blocked')
            ->where(function ($query) {
                $query->where('seats_available', '>', 0)
                    ->orWhere('is_exclusive_booked', false);
            })
            ->pluck('date')
            ->unique()
            ->values()
            ->toArray();

        // Get similar catamarans
        $similarCatamarans = Catamaran::where('is_active', true)
            ->where('id', '!=', $catamaran->id)
            ->orderBy('sort_order')
            ->with(['images' => fn($q) => $q->where('is_primary', true)])
            ->take(3)
            ->get();

        return view('catamarans.show', compact('catamaran', 'availableDates', 'similarCatamarans'));
    }

    /**
     * Check availability for a specific date and catamaran.
     */
    public function checkAvailability(Request $request, string $slug)
    {
        $catamaran = Catamaran::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $availability = Availability::where('catamaran_id', $catamaran->id)
            ->where('date', $request->date)
            ->with('timeSlot')
            ->get();

        return response()->json([
            'catamaran' => $catamaran->only(['id', 'name', 'slug', 'capacity']),
            'date' => $request->date,
            'slots' => $availability->map(fn($a) => [
                'time_slot' => $a->timeSlot,
                'seats_available' => $a->seats_available - $a->seats_booked,
                'is_exclusive_available' => !$a->is_exclusive_booked,
                'status' => $a->status,
            ]),
        ]);
    }
}
