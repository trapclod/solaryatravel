{{-- Form fields for addons --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
    <h2 class="text-lg font-semibold text-gray-900 border-b pb-3">Informazioni Base</h2>
    
    {{-- Name --}}
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
            Nome <span class="text-red-500">*</span>
        </label>
        <input type="text" 
               name="name" 
               id="name" 
               value="{{ old('name', $addon->name ?? '') }}"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror"
               placeholder="es. Aperitivo al tramonto"
               required>
        @error('name')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Slug --}}
    <div>
        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
            Slug
        </label>
        <input type="text" 
               name="slug" 
               id="slug" 
               value="{{ old('slug', $addon->slug ?? '') }}"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('slug') border-red-500 @enderror"
               placeholder="aperitivo-tramonto">
        <p class="text-xs text-gray-500 mt-1">Lascia vuoto per generazione automatica</p>
        @error('slug')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Description --}}
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
            Descrizione
        </label>
        <textarea name="description" 
                  id="description" 
                  rows="3"
                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-500 @enderror"
                  placeholder="Descrivi l'extra...">{{ old('description', $addon->description ?? '') }}</textarea>
        @error('description')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Image --}}
    <div>
        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
            Immagine
        </label>
        @if(isset($addon) && $addon->image_path)
            <div class="mb-3">
                <img src="{{ Storage::url($addon->image_path) }}" 
                     alt="{{ $addon->name }}" 
                     class="w-32 h-32 object-cover rounded-lg">
            </div>
        @endif
        <input type="file" 
               name="image" 
               id="image" 
               accept="image/jpeg,image/png,image/jpg,image/webp"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('image') border-red-500 @enderror">
        <p class="text-xs text-gray-500 mt-1">JPG, PNG o WebP. Max 2MB</p>
        @error('image')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- Pricing --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
    <h2 class="text-lg font-semibold text-gray-900 border-b pb-3">Prezzo</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Price --}}
        <div>
            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                Prezzo (€) <span class="text-red-500">*</span>
            </label>
            <input type="number" 
                   name="price" 
                   id="price" 
                   value="{{ old('price', $addon->price ?? '0') }}"
                   step="0.01"
                   min="0"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('price') border-red-500 @enderror"
                   required>
            @error('price')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Price Type --}}
        <div>
            <label for="price_type" class="block text-sm font-medium text-gray-700 mb-2">
                Tipo Prezzo <span class="text-red-500">*</span>
            </label>
            <select name="price_type" 
                    id="price_type"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('price_type') border-red-500 @enderror"
                    required>
                <option value="per_person" {{ old('price_type', $addon->price_type ?? '') == 'per_person' ? 'selected' : '' }}>Per persona</option>
                <option value="per_booking" {{ old('price_type', $addon->price_type ?? '') == 'per_booking' ? 'selected' : '' }}>Per prenotazione</option>
                <option value="per_unit" {{ old('price_type', $addon->price_type ?? '') == 'per_unit' ? 'selected' : '' }}>Per unità</option>
            </select>
            @error('price_type')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Max Quantity --}}
        <div>
            <label for="max_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                Quantità Massima
            </label>
            <input type="number" 
                   name="max_quantity" 
                   id="max_quantity" 
                   value="{{ old('max_quantity', $addon->max_quantity ?? '') }}"
                   min="1"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('max_quantity') border-red-500 @enderror"
                   placeholder="Illimitata">
            <p class="text-xs text-gray-500 mt-1">Lascia vuoto per quantità illimitata</p>
            @error('max_quantity')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Sort Order --}}
        <div>
            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                Ordine
            </label>
            <input type="number" 
                   name="sort_order" 
                   id="sort_order" 
                   value="{{ old('sort_order', $addon->sort_order ?? 0) }}"
                   min="0"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('sort_order') border-red-500 @enderror">
            @error('sort_order')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

{{-- Advanced Options --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
    <h2 class="text-lg font-semibold text-gray-900 border-b pb-3">Opzioni Avanzate</h2>
    
    <div class="space-y-4">
        {{-- Is Active --}}
        <label class="flex items-center gap-3">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" 
                   name="is_active" 
                   value="1"
                   class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                   {{ old('is_active', $addon->is_active ?? true) ? 'checked' : '' }}>
            <span class="text-sm font-medium text-gray-700">Attivo</span>
            <span class="text-sm text-gray-500">- L'extra sarà disponibile per le prenotazioni</span>
        </label>

        {{-- Requires Advance Booking --}}
        <label class="flex items-center gap-3">
            <input type="hidden" name="requires_advance_booking" value="0">
            <input type="checkbox" 
                   name="requires_advance_booking" 
                   value="1"
                   id="requires_advance_booking"
                   class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                   {{ old('requires_advance_booking', $addon->requires_advance_booking ?? false) ? 'checked' : '' }}>
            <span class="text-sm font-medium text-gray-700">Richiede prenotazione anticipata</span>
        </label>

        {{-- Advance Hours --}}
        <div id="advance_hours_container" class="ml-8 {{ old('requires_advance_booking', $addon->requires_advance_booking ?? false) ? '' : 'hidden' }}">
            <label for="advance_hours" class="block text-sm font-medium text-gray-700 mb-2">
                Ore di anticipo richieste
            </label>
            <input type="number" 
                   name="advance_hours" 
                   id="advance_hours" 
                   value="{{ old('advance_hours', $addon->advance_hours ?? 24) }}"
                   min="0"
                   class="w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <p class="text-xs text-gray-500 mt-1">Quante ore prima della partenza deve essere prenotato</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('requires_advance_booking').addEventListener('change', function() {
        document.getElementById('advance_hours_container').classList.toggle('hidden', !this.checked);
    });
</script>
@endpush
