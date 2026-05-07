<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\TourDeparture;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * Listing pubblico dei tour, con search opzionale per data/persone.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'date' => 'nullable|date',
            'guests' => 'nullable|integer|min:1|max:200',
            'sort' => 'nullable|in:default,price_asc,price_desc,duration',
        ]);
        $date = $validated['date'] ?? null;
        $guests = (int) ($validated['guests'] ?? 0);
        $sort = $validated['sort'] ?? 'default';
        $isSearch = $request->filled('date') || $request->filled('guests');

        $query = Tour::active()
            ->ordered()
            ->with(['images' => fn ($q) => $q->orderBy('sort_order')]);

        $tours = $query->get();

        // Filtra per stagione se data specificata
        if ($date) {
            $tours = $tours->filter(function (Tour $t) use ($date) {
                if ($t->season_start && $date < $t->season_start->toDateString()) {
                    return false;
                }
                if ($t->season_end && $date > $t->season_end->toDateString()) {
                    return false;
                }
                return true;
            })->values();
        }

        // Pre-carica fasce per il "from" price
        $tours->load('ageBrackets');

        // Sorting
        $tours = match ($sort) {
            'price_asc' => $tours->sortBy(fn (Tour $t) => $t->price_from ?? PHP_INT_MAX)->values(),
            'price_desc' => $tours->sortByDesc(fn (Tour $t) => $t->price_from ?? 0)->values(),
            'duration' => $tours->sortBy(fn (Tour $t) => (float) $t->duration_hours)->values(),
            default => $tours,
        };

        $search = [
            'isSearch' => $isSearch,
            'date' => $date,
            'guests' => $guests,
            'sort' => $sort,
            'results' => $tours->count(),
        ];

        return view('tours.index', compact('tours', 'search'));
    }

    public function show(string $slug, Request $request): View
    {
        $tour = Tour::active()->where('slug', $slug)
            ->with(['images', 'ageBrackets', 'periods', 'catamarans'])
            ->firstOrFail();

        // Prossime partenze (60 giorni) generate dai periodi
        $departures = app(\App\Services\DepartureGeneratorService::class)
            ->upcoming($tour, 60);

        // Tour simili
        $similar = Tour::active()
            ->where('id', '!=', $tour->id)
            ->ordered()
            ->with(['images' => fn ($q) => $q->where('is_primary', true)])
            ->take(3)
            ->get();

        return view('tours.show', compact('tour', 'departures', 'similar'));
    }

    /**
     * Endpoint AJAX: posti disponibili per una partenza.
     */
    public function checkDeparture(TourDeparture $departure, Request $request)
    {
        $request->validate(['seats' => 'required|integer|min:1|max:200']);
        $result = $this->bookingService->checkAvailability($departure, (int) $request->seats);
        return response()->json($result + [
            'departure_id' => $departure->id,
            'seats_available' => $departure->seats_available,
        ]);
    }
}
