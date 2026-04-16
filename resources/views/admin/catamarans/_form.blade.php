{{-- Basic Information --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informazioni Base</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-2">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Nome <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   name="name" 
                   id="name" 
                   value="{{ old('name', $catamaran->name ?? '') }}"
                   required
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror">
            @error('name')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                Slug URL
            </label>
            <input type="text" 
                   name="slug" 
                   id="slug" 
                   value="{{ old('slug', $catamaran->slug ?? '') }}"
                   placeholder="Generato automaticamente"
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('slug') border-red-500 @enderror">
            @error('slug')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">
                Ordine
            </label>
            <input type="number" 
                   name="sort_order" 
                   id="sort_order" 
                   value="{{ old('sort_order', $catamaran->sort_order ?? 0) }}"
                   min="0"
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>

        <div class="md:col-span-2">
            <label for="description_short" class="block text-sm font-medium text-gray-700 mb-1">
                Descrizione Breve
            </label>
            <textarea name="description_short" 
                      id="description_short" 
                      rows="2"
                      maxlength="500"
                      class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description_short', $catamaran->description_short ?? '') }}</textarea>
        </div>

        <div class="md:col-span-2">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                Descrizione Completa
            </label>
            <textarea name="description" 
                      id="description" 
                      rows="5"
                      class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description', $catamaran->description ?? '') }}</textarea>
        </div>
    </div>
</div>

{{-- Specifications --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Specifiche Tecniche</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="capacity" class="block text-sm font-medium text-gray-700 mb-1">
                Capacità (posti) <span class="text-red-500">*</span>
            </label>
            <input type="number" 
                   name="capacity" 
                   id="capacity" 
                   value="{{ old('capacity', $catamaran->capacity ?? 12) }}"
                   required
                   min="1"
                   max="100"
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('capacity') border-red-500 @enderror">
            @error('capacity')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="length_meters" class="block text-sm font-medium text-gray-700 mb-1">
                Lunghezza (metri)
            </label>
            <input type="number" 
                   name="length_meters" 
                   id="length_meters" 
                   value="{{ old('length_meters', $catamaran->length_meters ?? '') }}"
                   step="0.1"
                   min="0"
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>
    </div>

    <div class="mt-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Caratteristiche
        </label>
        <div id="features-container" class="space-y-2">
            @php
                $rawFeatures = old('features', $catamaran->features ?? []);
                // Handle both array and JSON string cases
                if (is_string($rawFeatures)) {
                    $features = json_decode($rawFeatures, true) ?? [];
                } else {
                    $features = is_array($rawFeatures) ? $rawFeatures : [];
                }
            @endphp
            @forelse($features as $index => $feature)
                <div class="flex items-center gap-2 feature-row">
                    <input type="text" 
                           name="features[]" 
                           value="{{ $feature }}"
                           placeholder="es. Solarium"
                           class="flex-1 px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <button type="button" onclick="removeFeature(this)" class="p-2 text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @empty
                <div class="flex items-center gap-2 feature-row">
                    <input type="text" 
                           name="features[]" 
                           placeholder="es. Solarium"
                           class="flex-1 px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <button type="button" onclick="removeFeature(this)" class="p-2 text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endforelse
        </div>
        <button type="button" onclick="addFeature()" 
                class="mt-2 text-sm text-primary-600 hover:text-primary-700 font-medium">
            + Aggiungi caratteristica
        </button>
    </div>
</div>

{{-- Pricing --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Prezzi</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="base_price_half_day" class="block text-sm font-medium text-gray-700 mb-1">
                Prezzo Base Mezza Giornata (€) <span class="text-red-500">*</span>
            </label>
            <input type="number" 
                   name="base_price_half_day" 
                   id="base_price_half_day" 
                   value="{{ old('base_price_half_day', $catamaran->base_price_half_day ?? '') }}"
                   required
                   step="0.01"
                   min="0"
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('base_price_half_day') border-red-500 @enderror">
            @error('base_price_half_day')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="base_price_full_day" class="block text-sm font-medium text-gray-700 mb-1">
                Prezzo Base Giornata Intera (€) <span class="text-red-500">*</span>
            </label>
            <input type="number" 
                   name="base_price_full_day" 
                   id="base_price_full_day" 
                   value="{{ old('base_price_full_day', $catamaran->base_price_full_day ?? '') }}"
                   required
                   step="0.01"
                   min="0"
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('base_price_full_day') border-red-500 @enderror">
            @error('base_price_full_day')
                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="exclusive_price_half_day" class="block text-sm font-medium text-gray-700 mb-1">
                Prezzo Esclusivo Mezza Giornata (€)
            </label>
            <input type="number" 
                   name="exclusive_price_half_day" 
                   id="exclusive_price_half_day" 
                   value="{{ old('exclusive_price_half_day', $catamaran->exclusive_price_half_day ?? '') }}"
                   step="0.01"
                   min="0"
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>

        <div>
            <label for="exclusive_price_full_day" class="block text-sm font-medium text-gray-700 mb-1">
                Prezzo Esclusivo Giornata Intera (€)
            </label>
            <input type="number" 
                   name="exclusive_price_full_day" 
                   id="exclusive_price_full_day" 
                   value="{{ old('exclusive_price_full_day', $catamaran->exclusive_price_full_day ?? '') }}"
                   step="0.01"
                   min="0"
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>

        <div>
            <label for="price_per_person_half_day" class="block text-sm font-medium text-gray-700 mb-1">
                Prezzo per Persona Mezza Giornata (€)
            </label>
            <input type="number" 
                   name="price_per_person_half_day" 
                   id="price_per_person_half_day" 
                   value="{{ old('price_per_person_half_day', $catamaran->price_per_person_half_day ?? '') }}"
                   step="0.01"
                   min="0"
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>

        <div>
            <label for="price_per_person_full_day" class="block text-sm font-medium text-gray-700 mb-1">
                Prezzo per Persona Giornata Intera (€)
            </label>
            <input type="number" 
                   name="price_per_person_full_day" 
                   id="price_per_person_full_day" 
                   value="{{ old('price_per_person_full_day', $catamaran->price_per_person_full_day ?? '') }}"
                   step="0.01"
                   min="0"
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>
    </div>
</div>

{{-- SEO --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">SEO</h2>
    
    <div class="space-y-4">
        <div>
            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">
                Meta Title
            </label>
            <input type="text" 
                   name="meta_title" 
                   id="meta_title" 
                   value="{{ old('meta_title', $catamaran->meta_title ?? '') }}"
                   maxlength="255"
                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        </div>

        <div>
            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">
                Meta Description
            </label>
            <textarea name="meta_description" 
                      id="meta_description" 
                      rows="2"
                      maxlength="500"
                      class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('meta_description', $catamaran->meta_description ?? '') }}</textarea>
        </div>
    </div>
</div>

{{-- Status --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Stato</h2>
            <p class="text-sm text-gray-500">Attiva o disattiva il catamarano sul sito</p>
        </div>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" 
                   name="is_active" 
                   value="1"
                   {{ old('is_active', $catamaran->is_active ?? true) ? 'checked' : '' }}
                   class="sr-only peer">
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
            <span class="ml-3 text-sm font-medium text-gray-900">Attivo</span>
        </label>
    </div>
</div>

@push('scripts')
<script>
function addFeature() {
    const container = document.getElementById('features-container');
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2 feature-row';
    row.innerHTML = `
        <input type="text" 
               name="features[]" 
               placeholder="es. Solarium"
               class="flex-1 px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        <button type="button" onclick="removeFeature(this)" class="p-2 text-red-500 hover:text-red-700">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;
    container.appendChild(row);
}

function removeFeature(button) {
    const rows = document.querySelectorAll('.feature-row');
    if (rows.length > 1) {
        button.closest('.feature-row').remove();
    }
}
</script>
@endpush
