{{-- Form fields for discount codes --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
    <h2 class="text-lg font-semibold text-gray-900 border-b pb-3">Codice e Descrizione</h2>
    
    {{-- Code --}}
    <div>
        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
            Codice <span class="text-red-500">*</span>
        </label>
        <div class="flex gap-2">
            <input type="text" 
                   name="code" 
                   id="code" 
                   value="{{ old('code', $discount->code ?? '') }}"
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-mono uppercase @error('code') border-red-500 @enderror"
                   placeholder="ES. SUMMER2024"
                   required>
            <button type="button" 
                    onclick="generateCode()"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                Genera
            </button>
        </div>
        <p class="text-xs text-gray-500 mt-1">Il codice verrà convertito automaticamente in maiuscolo</p>
        @error('code')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Description --}}
    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
            Descrizione
        </label>
        <input type="text" 
               name="description" 
               id="description" 
               value="{{ old('description', $discount->description ?? '') }}"
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-500 @enderror"
               placeholder="es. Sconto estate 2024">
        @error('description')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

{{-- Discount Details --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
    <h2 class="text-lg font-semibold text-gray-900 border-b pb-3">Dettagli Sconto</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Discount Type --}}
        <div>
            <label for="discount_type" class="block text-sm font-medium text-gray-700 mb-2">
                Tipo Sconto <span class="text-red-500">*</span>
            </label>
            <select name="discount_type" 
                    id="discount_type"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('discount_type') border-red-500 @enderror"
                    required>
                <option value="percentage" {{ old('discount_type', $discount->discount_type ?? 'percentage') == 'percentage' ? 'selected' : '' }}>Percentuale (%)</option>
                <option value="fixed" {{ old('discount_type', $discount->discount_type ?? '') == 'fixed' ? 'selected' : '' }}>Fisso (€)</option>
            </select>
            @error('discount_type')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Discount Value --}}
        <div>
            <label for="discount_value" class="block text-sm font-medium text-gray-700 mb-2">
                Valore Sconto <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input type="number" 
                       name="discount_value" 
                       id="discount_value" 
                       value="{{ old('discount_value', $discount->discount_value ?? '') }}"
                       step="0.01"
                       min="0"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('discount_value') border-red-500 @enderror"
                       required>
                <span id="discount_suffix" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">%</span>
            </div>
            @error('discount_value')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Min Amount --}}
        <div>
            <label for="min_amount" class="block text-sm font-medium text-gray-700 mb-2">
                Importo Minimo (€)
            </label>
            <input type="number" 
                   name="min_amount" 
                   id="min_amount" 
                   value="{{ old('min_amount', $discount->min_amount ?? '') }}"
                   step="0.01"
                   min="0"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('min_amount') border-red-500 @enderror"
                   placeholder="Nessun minimo">
            <p class="text-xs text-gray-500 mt-1">Importo minimo dell'ordine per applicare lo sconto</p>
            @error('min_amount')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Max Discount --}}
        <div>
            <label for="max_discount" class="block text-sm font-medium text-gray-700 mb-2">
                Sconto Massimo (€)
            </label>
            <input type="number" 
                   name="max_discount" 
                   id="max_discount" 
                   value="{{ old('max_discount', $discount->max_discount ?? '') }}"
                   step="0.01"
                   min="0"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('max_discount') border-red-500 @enderror"
                   placeholder="Nessun limite">
            <p class="text-xs text-gray-500 mt-1">Limite massimo di sconto applicabile (per sconti percentuali)</p>
            @error('max_discount')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

{{-- Usage Limits --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
    <h2 class="text-lg font-semibold text-gray-900 border-b pb-3">Limiti di Utilizzo</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Usage Limit --}}
        <div>
            <label for="usage_limit" class="block text-sm font-medium text-gray-700 mb-2">
                Utilizzi Totali
            </label>
            <input type="number" 
                   name="usage_limit" 
                   id="usage_limit" 
                   value="{{ old('usage_limit', $discount->usage_limit ?? '') }}"
                   min="1"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('usage_limit') border-red-500 @enderror"
                   placeholder="Illimitati">
            <p class="text-xs text-gray-500 mt-1">Numero massimo di utilizzi totali</p>
            @error('usage_limit')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- User Limit --}}
        <div>
            <label for="user_limit" class="block text-sm font-medium text-gray-700 mb-2">
                Utilizzi per Utente
            </label>
            <input type="number" 
                   name="user_limit" 
                   id="user_limit" 
                   value="{{ old('user_limit', $discount->user_limit ?? 1) }}"
                   min="1"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('user_limit') border-red-500 @enderror">
            <p class="text-xs text-gray-500 mt-1">Quante volte un singolo utente può usare questo codice</p>
            @error('user_limit')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>

    @if(isset($discount) && $discount->usage_count > 0)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-800">
                <strong>Nota:</strong> Questo codice è stato già utilizzato {{ $discount->usage_count }} 
                {{ $discount->usage_count == 1 ? 'volta' : 'volte' }}.
            </p>
        </div>
    @endif
</div>

{{-- Validity Period --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
    <h2 class="text-lg font-semibold text-gray-900 border-b pb-3">Periodo di Validità</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Valid From --}}
        <div>
            <label for="valid_from" class="block text-sm font-medium text-gray-700 mb-2">
                Valido Da
            </label>
            <input type="datetime-local" 
                   name="valid_from" 
                   id="valid_from" 
                   value="{{ old('valid_from', isset($discount->valid_from) ? $discount->valid_from->format('Y-m-d\TH:i') : '') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('valid_from') border-red-500 @enderror">
            <p class="text-xs text-gray-500 mt-1">Lascia vuoto per attivazione immediata</p>
            @error('valid_from')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Valid Until --}}
        <div>
            <label for="valid_until" class="block text-sm font-medium text-gray-700 mb-2">
                Valido Fino A
            </label>
            <input type="datetime-local" 
                   name="valid_until" 
                   id="valid_until" 
                   value="{{ old('valid_until', isset($discount->valid_until) ? $discount->valid_until->format('Y-m-d\TH:i') : '') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('valid_until') border-red-500 @enderror">
            <p class="text-xs text-gray-500 mt-1">Lascia vuoto per nessuna scadenza</p>
            @error('valid_until')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

{{-- Status --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
    <h2 class="text-lg font-semibold text-gray-900 border-b pb-3">Stato</h2>
    
    <label class="flex items-center gap-3">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" 
               name="is_active" 
               value="1"
               class="w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
               {{ old('is_active', $discount->is_active ?? true) ? 'checked' : '' }}>
        <span class="text-sm font-medium text-gray-700">Codice attivo</span>
        <span class="text-sm text-gray-500">- Il codice sarà utilizzabile dai clienti</span>
    </label>
</div>

@push('scripts')
<script>
    // Update suffix based on discount type
    document.getElementById('discount_type').addEventListener('change', function() {
        const suffix = document.getElementById('discount_suffix');
        suffix.textContent = this.value === 'percentage' ? '%' : '€';
    });
    
    // Initialize suffix
    document.addEventListener('DOMContentLoaded', function() {
        const type = document.getElementById('discount_type');
        const suffix = document.getElementById('discount_suffix');
        suffix.textContent = type.value === 'percentage' ? '%' : '€';
    });

    // Generate random code
    function generateCode() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let code = '';
        for (let i = 0; i < 8; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('code').value = code;
    }
</script>
@endpush
