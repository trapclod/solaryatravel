<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catamaran;
use App\Models\Tour;
use App\Models\TourAgeBracket;
use App\Models\TourImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TourController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tour::with(['ageBrackets', 'images' => fn ($q) => $q->where('is_primary', true)])
            ->withCount(['departures', 'bookings']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $tours = $query->orderBy('sort_order')->orderBy('name')->paginate(20)->withQueryString();

        return view('admin.tours.index', compact('tours'));
    }

    public function create(): View
    {
        $tour = new Tour([
            'is_active' => true,
            'min_capacity' => 1,
            'duration_hours' => 4,
        ]);
        $catamarans = Catamaran::active()->ordered()->get();
        $selectedCatamarans = [];
        return view('admin.tours.create', compact('tour', 'catamarans', 'selectedCatamarans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        return DB::transaction(function () use ($data, $request) {
            $tour = Tour::create($this->extractTourFields($data));
            $this->syncAgeBrackets($tour, $data['age_brackets'] ?? []);
            $this->syncCatamarans($tour, $data['catamarans'] ?? []);
            $this->handleImages($tour, $request);
            return redirect()->route('admin.tours.edit', $tour)
                ->with('success', 'Tour creato con successo.');
        });
    }

    public function show(Tour $tour): View
    {
        $tour->load(['ageBrackets', 'images', 'catamarans', 'departures' => fn ($q) => $q->orderBy('departure_date')->orderBy('start_time')]);
        return view('admin.tours.show', compact('tour'));
    }

    public function edit(Tour $tour): View
    {
        $tour->load(['ageBrackets', 'images', 'catamarans']);
        $catamarans = Catamaran::active()->ordered()->get();
        $selectedCatamarans = $tour->catamarans->pluck('id')->all();
        return view('admin.tours.edit', compact('tour', 'catamarans', 'selectedCatamarans'));
    }

    public function update(Request $request, Tour $tour): RedirectResponse
    {
        $data = $this->validateData($request, $tour);
        return DB::transaction(function () use ($data, $tour, $request) {
            $tour->update($this->extractTourFields($data));
            $this->syncAgeBrackets($tour, $data['age_brackets'] ?? []);
            $this->syncCatamarans($tour, $data['catamarans'] ?? []);
            $this->handleImages($tour, $request);
            return redirect()->route('admin.tours.edit', $tour)
                ->with('success', 'Tour aggiornato.');
        });
    }

    public function destroy(Tour $tour): RedirectResponse
    {
        if ($tour->bookings()->exists()) {
            return back()->with('error', 'Impossibile eliminare: il tour ha prenotazioni associate.');
        }
        $tour->delete();
        return redirect()->route('admin.tours.index')->with('success', 'Tour eliminato.');
    }

    public function toggle(Tour $tour): RedirectResponse
    {
        $tour->update(['is_active' => !$tour->is_active]);
        return back()->with('success', $tour->is_active ? 'Tour attivato.' : 'Tour disattivato.');
    }

    public function deleteImage(Tour $tour, TourImage $image): RedirectResponse
    {
        if ($image->tour_id !== $tour->id) {
            abort(404);
        }
        if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }
        $image->delete();
        return back()->with('success', 'Immagine eliminata.');
    }

    public function setPrimaryImage(Tour $tour, TourImage $image): RedirectResponse
    {
        if ($image->tour_id !== $tour->id) {
            abort(404);
        }
        $tour->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);
        return back()->with('success', 'Immagine principale aggiornata.');
    }

    // ----- Helpers -----

    protected function validateData(Request $request, ?Tour $tour = null): array
    {
        $slugRule = 'nullable|string|max:255';
        if ($tour) {
            $slugRule .= '|unique:tours,slug,' . $tour->id;
        } else {
            $slugRule .= '|unique:tours,slug';
        }

        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => $slugRule,
            'description' => 'nullable|string',
            'description_short' => 'nullable|string|max:500',
            'duration_hours' => 'nullable|numeric|min:0|max:48',
            'departure_point' => 'nullable|string|max:255',
            'itinerary' => 'nullable|string',
            'included' => 'nullable|array',
            'included.*' => 'string|max:255',
            'excluded' => 'nullable|array',
            'excluded.*' => 'string|max:255',
            'season_start' => 'nullable|date',
            'season_end' => 'nullable|date|after_or_equal:season_start',
            'min_capacity' => 'nullable|integer|min:1|max:200',
            'max_capacity' => 'nullable|integer|min:1|max:1000',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',

            'age_brackets' => 'nullable|array',
            'age_brackets.*.id' => 'nullable|integer|exists:tour_age_brackets,id',
            'age_brackets.*.label' => 'required_with:age_brackets|string|max:100',
            'age_brackets.*.min_age' => 'nullable|integer|min:0|max:120',
            'age_brackets.*.max_age' => 'nullable|integer|min:0|max:120',
            'age_brackets.*.price' => 'required_with:age_brackets|numeric|min:0|max:99999',
            'age_brackets.*.counts_as_seat' => 'sometimes|boolean',
            'age_brackets.*.sort_order' => 'nullable|integer',

            'catamarans' => 'nullable|array',
            'catamarans.*' => 'integer|exists:catamarans,id',

            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
        ]);
    }

    protected function extractTourFields(array $data): array
    {
        $fields = [
            'name', 'description', 'description_short', 'duration_hours',
            'departure_point', 'itinerary', 'included', 'excluded',
            'season_start', 'season_end', 'min_capacity', 'max_capacity',
            'sort_order', 'meta_title', 'meta_description',
        ];
        $out = [];
        foreach ($fields as $f) {
            if (array_key_exists($f, $data)) {
                $out[$f] = $data[$f];
            }
        }
        $out['slug'] = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);
        $out['is_active'] = !empty($data['is_active']);
        return $out;
    }

    protected function syncAgeBrackets(Tour $tour, array $brackets): void
    {
        $keepIds = [];
        foreach (array_values($brackets) as $idx => $b) {
            $payload = [
                'tour_id' => $tour->id,
                'label' => $b['label'],
                'min_age' => $b['min_age'] ?? 0,
                'max_age' => $b['max_age'] ?? null,
                'price' => $b['price'],
                'counts_as_seat' => !empty($b['counts_as_seat']),
                'sort_order' => $b['sort_order'] ?? $idx,
            ];
            if (!empty($b['id'])) {
                $existing = TourAgeBracket::where('id', $b['id'])->where('tour_id', $tour->id)->first();
                if ($existing) {
                    $existing->update($payload);
                    $keepIds[] = $existing->id;
                    continue;
                }
            }
            $created = TourAgeBracket::create($payload);
            $keepIds[] = $created->id;
        }
        // Elimina quelli rimossi
        $tour->ageBrackets()->whereNotIn('id', $keepIds)->delete();
    }

    protected function syncCatamarans(Tour $tour, array $ids): void
    {
        $payload = [];
        foreach (array_values($ids) as $idx => $catId) {
            $payload[(int) $catId] = ['priority' => $idx];
        }
        $tour->catamarans()->sync($payload);
    }

    protected function handleImages(Tour $tour, Request $request): void
    {
        if (!$request->hasFile('images')) {
            return;
        }
        $hasPrimary = $tour->images()->where('is_primary', true)->exists();
        $sort = (int) $tour->images()->max('sort_order') + 1;
        foreach ($request->file('images') as $file) {
            $path = $file->store('tours/' . $tour->id, 'public');
            TourImage::create([
                'tour_id' => $tour->id,
                'image_path' => $path,
                'image_alt' => $tour->name,
                'is_primary' => !$hasPrimary,
                'sort_order' => $sort++,
            ]);
            $hasPrimary = true;
        }
    }
}
