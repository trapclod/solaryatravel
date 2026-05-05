<?php

namespace App\Http\Controllers;

use App\Models\Addon;
use App\Models\Catamaran;
use App\Models\Availability;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatamaranController extends Controller
{
    /**
     * Display a listing of catamarans.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'date' => 'nullable|date|after_or_equal:today',
            'adults' => 'nullable|integer|min:1|max:20',
            'children' => 'nullable|integer|min:0|max:20',
            'slot_type' => 'nullable|in:half_day,full_day',
            'capacity_filter' => 'nullable|in:all,small,medium,large',
            'features' => 'nullable|array',
            'features.*' => 'string|max:100',
            'sort' => 'nullable|in:default,price_asc,price_desc,capacity_desc,capacity_asc',
        ]);

        $date = $validated['date'] ?? null;
        $adults = (int) ($validated['adults'] ?? 2);
        $children = (int) ($validated['children'] ?? 0);
        $slotType = $validated['slot_type'] ?? null;
        $capacityFilter = $validated['capacity_filter'] ?? 'all';
        $featuresFilter = array_values(array_filter($validated['features'] ?? [], fn ($f) => is_string($f) && $f !== ''));
        $sort = $validated['sort'] ?? 'default';
        $guests = max(1, $adults + $children);

        $isAvailabilitySearch = $request->filled('date');

        $query = Catamaran::where('is_active', true)
            ->orderBy('sort_order')
            ->with(['images']);

        // Capacity filter
        if ($capacityFilter === 'small') {
            $query->where('capacity', '<=', 8);
        } elseif ($capacityFilter === 'medium') {
            $query->whereBetween('capacity', [9, 14]);
        } elseif ($capacityFilter === 'large') {
            $query->where('capacity', '>=', 15);
        }

        $availabilitySummary = [];

        if ($isAvailabilitySearch && $date) {
            $availabilityQuery = Availability::query()
                ->where('date', $date)
                ->where('status', '!=', 'blocked')
                ->where('is_exclusive_booked', false)
                ->whereRaw('(seats_available - seats_booked) >= ?', [$guests])
                ->whereHas('timeSlot', function ($timeSlotQuery) use ($slotType) {
                    $timeSlotQuery->where('is_active', true);

                    if ($slotType) {
                        $timeSlotQuery->where('slot_type', $slotType);
                    }
                })
                ->selectRaw('catamaran_id, MAX(seats_available - seats_booked) as max_remaining_seats')
                ->groupBy('catamaran_id')
                ->get();

            $availabilitySummary = $availabilityQuery
                ->pluck('max_remaining_seats', 'catamaran_id')
                ->map(fn ($value) => (int) $value)
                ->toArray();

            $query->whereIn('id', array_keys($availabilitySummary));
        }

        $catamarans = $query->get();

        // Helper: features may be double-encoded in DB; normalize to array of strings.
        $normalizeFeatures = function ($value): array {
            if (is_array($value)) {
                $arr = $value;
            } elseif (is_string($value)) {
                $decoded = json_decode($value, true);
                if (is_string($decoded)) {
                    $decoded = json_decode($decoded, true);
                }
                $arr = is_array($decoded) ? $decoded : [];
            } else {
                $arr = [];
            }
            return collect($arr)
                ->map(fn ($f) => is_string($f) ? $f : ($f['name'] ?? null))
                ->filter()
                ->values()
                ->all();
        };

        // Features filter
        if (!empty($featuresFilter)) {
            $catamarans = $catamarans->filter(function ($catamaran) use ($featuresFilter, $normalizeFeatures) {
                $catFeatures = $normalizeFeatures($catamaran->features);
                foreach ($featuresFilter as $required) {
                    if (!in_array($required, $catFeatures, true)) {
                        return false;
                    }
                }
                return true;
            })->values();
        }

        if ($isAvailabilitySearch && $date) {
            foreach ($catamarans as $catamaran) {
                $catamaran->matched_seats_available = $availabilitySummary[$catamaran->id] ?? 0;
            }

            $catamarans = $catamarans
                ->sortBy([
                    fn ($catamaran) => $catamaran->capacity < $guests ? 1 : 0,
                    fn ($catamaran) => abs($catamaran->capacity - $guests),
                    fn ($catamaran) => $catamaran->sort_order,
                ])
                ->values();
        }

        // Sorting (overrides default if specified)
        if ($sort !== 'default') {
            $catamarans = match ($sort) {
                'price_asc' => $catamarans->sortBy(fn ($c) => (float) ($c->price_per_person_half_day ?? $c->base_price_half_day ?? 0))->values(),
                'price_desc' => $catamarans->sortByDesc(fn ($c) => (float) ($c->price_per_person_half_day ?? $c->base_price_half_day ?? 0))->values(),
                'capacity_desc' => $catamarans->sortByDesc('capacity')->values(),
                'capacity_asc' => $catamarans->sortBy('capacity')->values(),
                default => $catamarans,
            };
        }

        // Aggregate available features for sidebar
        $availableFeatures = Catamaran::where('is_active', true)
            ->get(['features'])
            ->flatMap(fn ($c) => $normalizeFeatures($c->features))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        $search = [
            'isAvailabilitySearch' => $isAvailabilitySearch,
            'date' => $date,
            'adults' => $adults,
            'children' => $children,
            'slot_type' => $slotType,
            'capacity_filter' => $capacityFilter,
            'features' => $featuresFilter,
            'sort' => $sort,
            'guests' => $guests,
            'results' => $catamarans->count(),
        ];

        return view('catamarans.index', compact('catamarans', 'search', 'availableFeatures'));
    }

    /**
     * Display the specified catamaran.
     */
    public function show(string $slug): View
    {
        $catamaran = Catamaran::where('slug', $slug)
            ->where('is_active', true)
            ->with(['images'])
            ->firstOrFail();

        // Get available addons
        $addons = Addon::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

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

        return view('catamarans.show', compact('catamaran', 'addons', 'availableDates', 'similarCatamarans'));
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
