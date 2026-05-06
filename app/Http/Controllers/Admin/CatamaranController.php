<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catamaran;
use App\Models\CatamaranImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CatamaranController extends Controller
{
    public function index(Request $request): View
    {
        $query = Catamaran::with(['images' => fn ($q) => $q->where('is_primary', true)]);
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $catamarans = $query->ordered()->paginate(20)->withQueryString();
        return view('admin.catamarans.index', compact('catamarans'));
    }

    public function create(): View
    {
        $catamaran = new Catamaran(['is_active' => true, 'capacity' => 12]);
        return view('admin.catamarans.create', compact('catamaran'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['slug'] = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);
        $data['is_active'] = !empty($data['is_active']);
        $catamaran = Catamaran::create($data);
        $this->handleImages($catamaran, $request);
        return redirect()->route('admin.catamarans.edit', $catamaran)
            ->with('success', 'Catamarano creato.');
    }

    public function show(Catamaran $catamaran): View
    {
        $catamaran->load(['images', 'tours']);

        $bookingIds = \App\Models\BookingSeat::where('catamaran_id', $catamaran->id)
            ->pluck('booking_id')->unique();

        $bookingsQuery = \App\Models\Booking::whereIn('id', $bookingIds);

        $stats = [
            'total_bookings' => (clone $bookingsQuery)->count(),
            'upcoming_bookings' => (clone $bookingsQuery)
                ->where('booking_date', '>=', now()->toDateString())
                ->whereNotIn('status', ['cancelled', 'refunded', 'no_show'])
                ->count(),
            'total_revenue' => (clone $bookingsQuery)
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('total_amount'),
        ];

        return view('admin.catamarans.show', compact('catamaran', 'stats'));
    }

    public function edit(Catamaran $catamaran): View
    {
        $catamaran->load(['images']);
        return view('admin.catamarans.edit', compact('catamaran'));
    }

    public function update(Request $request, Catamaran $catamaran): RedirectResponse
    {
        $data = $this->validateData($request, $catamaran);
        $data['slug'] = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);
        $data['is_active'] = !empty($data['is_active']);
        $catamaran->update($data);
        $this->handleImages($catamaran, $request);
        return redirect()->route('admin.catamarans.edit', $catamaran)
            ->with('success', 'Catamarano aggiornato.');
    }

    public function destroy(Catamaran $catamaran): RedirectResponse
    {
        $catamaran->delete();
        return redirect()->route('admin.catamarans.index')->with('success', 'Catamarano eliminato.');
    }

    public function toggle(Catamaran $catamaran): RedirectResponse
    {
        $catamaran->update(['is_active' => !$catamaran->is_active]);
        return back()->with('success', 'Stato aggiornato.');
    }

    public function uploadImages(Request $request, Catamaran $catamaran): RedirectResponse
    {
        $request->validate(['images.*' => 'image|max:5120']);
        $this->handleImages($catamaran, $request);
        return back()->with('success', 'Immagini caricate.');
    }

    public function deleteImage(Catamaran $catamaran, CatamaranImage $image): RedirectResponse
    {
        if ($image->catamaran_id !== $catamaran->id) {
            abort(404);
        }
        if ($image->image_path && Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }
        $image->delete();
        return back()->with('success', 'Immagine eliminata.');
    }

    public function reorderImages(Request $request, Catamaran $catamaran): RedirectResponse
    {
        $order = $request->input('order', []);
        foreach ($order as $idx => $imageId) {
            $catamaran->images()->where('id', $imageId)->update(['sort_order' => $idx]);
        }
        return back();
    }

    protected function validateData(Request $request, ?Catamaran $catamaran = null): array
    {
        $slugRule = 'nullable|string|max:255';
        $slugRule .= $catamaran ? '|unique:catamarans,slug,' . $catamaran->id : '|unique:catamarans,slug';

        return $request->validate([
            'name' => 'required|string|max:255',
            'slug' => $slugRule,
            'description' => 'nullable|string',
            'description_short' => 'nullable|string|max:500',
            'capacity' => 'required|integer|min:1|max:200',
            'length_meters' => 'nullable|numeric|min:0|max:99',
            'features' => 'nullable|array',
            'features.*' => 'string|max:100',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
        ]);
    }

    protected function handleImages(Catamaran $catamaran, Request $request): void
    {
        if (!$request->hasFile('images')) {
            return;
        }
        $hasPrimary = $catamaran->images()->where('is_primary', true)->exists();
        $sort = (int) $catamaran->images()->max('sort_order') + 1;
        foreach ($request->file('images') as $file) {
            $path = $file->store('catamarans/' . $catamaran->id, 'public');
            CatamaranImage::create([
                'catamaran_id' => $catamaran->id,
                'image_path' => $path,
                'image_alt' => $catamaran->name,
                'is_primary' => !$hasPrimary,
                'sort_order' => $sort++,
            ]);
            $hasPrimary = true;
        }
    }
}
