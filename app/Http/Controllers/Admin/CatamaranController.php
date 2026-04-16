<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catamaran;
use App\Models\CatamaranImage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CatamaranController extends Controller
{
    /**
     * Display a listing of catamarans.
     */
    public function index(): View
    {
        $catamarans = Catamaran::withCount('bookings')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.catamarans.index', compact('catamarans'));
    }

    /**
     * Show the form for creating a new catamaran.
     */
    public function create(): View
    {
        return view('admin.catamarans.create');
    }

    /**
     * Store a newly created catamaran.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:catamarans,slug',
            'description' => 'nullable|string',
            'description_short' => 'nullable|string|max:500',
            'capacity' => 'required|integer|min:1|max:100',
            'length_meters' => 'nullable|numeric|min:0',
            'features' => 'nullable|array',
            'base_price_half_day' => 'required|numeric|min:0',
            'base_price_full_day' => 'required|numeric|min:0',
            'exclusive_price_half_day' => 'nullable|numeric|min:0',
            'exclusive_price_full_day' => 'nullable|numeric|min:0',
            'price_per_person_half_day' => 'nullable|numeric|min:0',
            'price_per_person_full_day' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Ensure unique slug
        $baseSlug = $validated['slug'];
        $counter = 1;
        while (Catamaran::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $baseSlug . '-' . $counter++;
        }

        $catamaran = Catamaran::create($validated);

        return redirect()
            ->route('admin.catamarans.show', $catamaran)
            ->with('success', 'Catamarano creato con successo.');
    }

    /**
     * Display the specified catamaran.
     */
    public function show(Catamaran $catamaran): View
    {
        $catamaran->load(['images', 'bookings' => function ($query) {
            $query->latest()->limit(10);
        }]);

        $stats = [
            'total_bookings' => $catamaran->bookings()->count(),
            'upcoming_bookings' => $catamaran->bookings()
                ->where('booking_date', '>=', now())
                ->where('status', '!=', 'cancelled')
                ->count(),
            'total_revenue' => $catamaran->bookings()
                ->whereHas('payments', function ($q) {
                    $q->where('status', 'succeeded');
                })
                ->sum('total_amount'),
        ];

        return view('admin.catamarans.show', compact('catamaran', 'stats'));
    }

    /**
     * Show the form for editing the specified catamaran.
     */
    public function edit(Catamaran $catamaran): View
    {
        $catamaran->load('images');
        return view('admin.catamarans.edit', compact('catamaran'));
    }

    /**
     * Update the specified catamaran.
     */
    public function update(Request $request, Catamaran $catamaran): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:catamarans,slug,' . $catamaran->id,
            'description' => 'nullable|string',
            'description_short' => 'nullable|string|max:500',
            'capacity' => 'required|integer|min:1|max:100',
            'length_meters' => 'nullable|numeric|min:0',
            'features' => 'nullable|array',
            'base_price_half_day' => 'required|numeric|min:0',
            'base_price_full_day' => 'required|numeric|min:0',
            'exclusive_price_half_day' => 'nullable|numeric|min:0',
            'exclusive_price_full_day' => 'nullable|numeric|min:0',
            'price_per_person_half_day' => 'nullable|numeric|min:0',
            'price_per_person_full_day' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $catamaran->update($validated);

        return redirect()
            ->route('admin.catamarans.show', $catamaran)
            ->with('success', 'Catamarano aggiornato con successo.');
    }

    /**
     * Remove the specified catamaran.
     */
    public function destroy(Catamaran $catamaran): RedirectResponse
    {
        // Check for active bookings
        $activeBookings = $catamaran->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('booking_date', '>=', now())
            ->count();

        if ($activeBookings > 0) {
            return back()->with('error', "Impossibile eliminare: ci sono {$activeBookings} prenotazioni attive.");
        }

        $catamaran->delete();

        return redirect()
            ->route('admin.catamarans.index')
            ->with('success', 'Catamarano eliminato con successo.');
    }

    /**
     * Toggle catamaran active status.
     */
    public function toggle(Catamaran $catamaran): RedirectResponse
    {
        $catamaran->update(['is_active' => !$catamaran->is_active]);

        $status = $catamaran->is_active ? 'attivato' : 'disattivato';

        return back()->with('success', "Catamarano {$status} con successo.");
    }

    /**
     * Upload images for the catamaran.
     */
    public function uploadImages(Request $request, Catamaran $catamaran): RedirectResponse
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $maxOrder = $catamaran->images()->max('sort_order') ?? 0;

        foreach ($request->file('images') as $image) {
            $path = $image->store('catamarans/' . $catamaran->id, 'public');
            
            $catamaran->images()->create([
                'path' => $path,
                'filename' => $image->getClientOriginalName(),
                'sort_order' => ++$maxOrder,
            ]);
        }

        return back()->with('success', 'Immagini caricate con successo.');
    }

    /**
     * Delete an image.
     */
    public function deleteImage(Catamaran $catamaran, CatamaranImage $image): RedirectResponse
    {
        // Verify the image belongs to this catamaran
        if ($image->catamaran_id !== $catamaran->id) {
            abort(404);
        }

        // Delete file from storage
        Storage::disk('public')->delete($image->path);
        
        $image->delete();

        return back()->with('success', 'Immagine eliminata con successo.');
    }

    /**
     * Reorder images.
     */
    public function reorderImages(Request $request, Catamaran $catamaran): RedirectResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:catamaran_images,id',
        ]);

        foreach ($request->order as $position => $imageId) {
            CatamaranImage::where('id', $imageId)
                ->where('catamaran_id', $catamaran->id)
                ->update(['sort_order' => $position]);
        }

        return back()->with('success', 'Ordine immagini aggiornato.');
    }
}
