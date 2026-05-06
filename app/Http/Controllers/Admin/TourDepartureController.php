<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourDeparture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TourDepartureController extends Controller
{
    public function index(Request $request, Tour $tour): View
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->addMonths(2)->endOfMonth()->toDateString());

        $departures = $tour->departures()
            ->whereBetween('departure_date', [$from, $to])
            ->orderBy('departure_date')
            ->orderBy('start_time')
            ->get();

        return view('admin.tours.departures.index', compact('tour', 'departures', 'from', 'to'));
    }

    public function store(Request $request, Tour $tour): RedirectResponse
    {
        $data = $this->validateData($request);

        // Supporta creazione singola o batch
        if ($data['mode'] === 'range') {
            $start = \Carbon\Carbon::parse($data['date_start']);
            $end = \Carbon\Carbon::parse($data['date_end']);
            $weekdays = $data['weekdays'] ?? [0, 1, 2, 3, 4, 5, 6];
            $created = 0;
            for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
                if (!in_array($d->dayOfWeek, array_map('intval', $weekdays))) {
                    continue;
                }
                $exists = TourDeparture::where('tour_id', $tour->id)
                    ->where('departure_date', $d->toDateString())
                    ->where('start_time', $data['start_time'])
                    ->exists();
                if ($exists) {
                    continue;
                }
                TourDeparture::create([
                    'tour_id' => $tour->id,
                    'departure_date' => $d->toDateString(),
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                    'status' => 'scheduled',
                    'price_modifier' => $data['price_modifier'] ?? 1,
                    'capacity_override' => $data['capacity_override'] ?? null,
                    'notes' => $data['notes'] ?? null,
                ]);
                $created++;
            }
            return back()->with('success', "Create {$created} partenze.");
        }

        TourDeparture::create([
            'tour_id' => $tour->id,
            'departure_date' => $data['date_start'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'status' => 'scheduled',
            'price_modifier' => $data['price_modifier'] ?? 1,
            'capacity_override' => $data['capacity_override'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
        return back()->with('success', 'Partenza creata.');
    }

    public function update(Request $request, Tour $tour, TourDeparture $departure): RedirectResponse
    {
        if ($departure->tour_id !== $tour->id) {
            abort(404);
        }
        $data = $request->validate([
            'departure_date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'status' => 'sometimes|in:scheduled,cancelled,sold_out',
            'price_modifier' => 'sometimes|numeric|min:0|max:10',
            'capacity_override' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:255',
        ]);
        $departure->update($data);
        return back()->with('success', 'Partenza aggiornata.');
    }

    public function destroy(Tour $tour, TourDeparture $departure): RedirectResponse
    {
        if ($departure->tour_id !== $tour->id) {
            abort(404);
        }
        if ($departure->bookings()->whereNotIn('status', ['cancelled', 'refunded', 'no_show'])->exists()) {
            return back()->with('error', 'Impossibile eliminare: ci sono prenotazioni attive.');
        }
        $departure->delete();
        return back()->with('success', 'Partenza eliminata.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'mode' => 'required|in:single,range',
            'date_start' => 'required|date',
            'date_end' => 'required_if:mode,range|nullable|date|after_or_equal:date_start',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'weekdays' => 'nullable|array',
            'weekdays.*' => 'integer|between:0,6',
            'price_modifier' => 'nullable|numeric|min:0|max:10',
            'capacity_override' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:255',
        ]);
    }
}
