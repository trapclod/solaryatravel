@extends('layouts.admin')

@section('title', 'Gestione Fasce Orarie')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.settings') }}" 
                   class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Fasce Orarie</h1>
                    <p class="text-gray-600">Gestisci gli slot di prenotazione disponibili</p>
                </div>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center gap-2 text-green-800">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.settings.timeslots.update') }}" method="POST" id="timeSlotsForm">
            @csrf

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Fasce Orarie Configurate</h2>
                    <button type="button" onclick="addTimeSlot()" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                        + Aggiungi Fascia
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ordine</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nome</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Orario Inizio</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Orario Fine</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tipo</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Modif. Prezzo</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Attivo</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Azioni</th>
                            </tr>
                        </thead>
                        <tbody id="timeSlotsBody" class="divide-y divide-gray-100">
                            @foreach($timeSlots as $index => $slot)
                                <tr class="hover:bg-gray-50 time-slot-row">
                                    <td class="px-4 py-3">
                                        <input type="hidden" name="slots[{{ $index }}][id]" value="{{ $slot->id }}">
                                        <input type="number" name="slots[{{ $index }}][sort_order]" value="{{ $slot->sort_order }}"
                                               class="w-16 px-2 py-1 border border-gray-300 rounded text-center text-sm"
                                               min="0" required>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="slots[{{ $index }}][name]" value="{{ $slot->name }}"
                                               class="w-full px-3 py-1 border border-gray-300 rounded text-sm"
                                               placeholder="Es. Mattina" required>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="time" name="slots[{{ $index }}][start_time]" 
                                               value="{{ $slot->start_time ? $slot->start_time->format('H:i') : '' }}"
                                               class="px-3 py-1 border border-gray-300 rounded text-sm" required>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="time" name="slots[{{ $index }}][end_time]" 
                                               value="{{ $slot->end_time ? $slot->end_time->format('H:i') : '' }}"
                                               class="px-3 py-1 border border-gray-300 rounded text-sm" required>
                                    </td>
                                    <td class="px-4 py-3">
                                        <select name="slots[{{ $index }}][slot_type]" 
                                                class="px-3 py-1 border border-gray-300 rounded text-sm">
                                            <option value="half_day" {{ $slot->slot_type === 'half_day' ? 'selected' : '' }}>Mezza Giornata</option>
                                            <option value="full_day" {{ $slot->slot_type === 'full_day' ? 'selected' : '' }}>Giornata Intera</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-1">
                                            <input type="number" name="slots[{{ $index }}][price_modifier]" 
                                                   value="{{ $slot->price_modifier }}"
                                                   class="w-20 px-2 py-1 border border-gray-300 rounded text-sm text-right"
                                                   step="0.01" min="0" max="10" required>
                                            <span class="text-gray-500 text-sm">x</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox" name="slots[{{ $index }}][is_active]" value="1"
                                               {{ $slot->is_active ? 'checked' : '' }}
                                               class="w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" onclick="removeTimeSlot(this)"
                                                class="text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($timeSlots->isEmpty())
                    <div class="px-6 py-12 text-center text-gray-500">
                        Nessuna fascia oraria configurata. Clicca "Aggiungi Fascia" per iniziare.
                    </div>
                @endif
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" 
                        class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium">
                    Salva Fasce Orarie
                </button>
            </div>
        </form>
    </div>

    <template id="timeSlotTemplate">
        <tr class="hover:bg-gray-50 time-slot-row">
            <td class="px-4 py-3">
                <input type="hidden" name="slots[INDEX][id]" value="">
                <input type="number" name="slots[INDEX][sort_order]" value="0"
                       class="w-16 px-2 py-1 border border-gray-300 rounded text-center text-sm"
                       min="0" required>
            </td>
            <td class="px-4 py-3">
                <input type="text" name="slots[INDEX][name]" value=""
                       class="w-full px-3 py-1 border border-gray-300 rounded text-sm"
                       placeholder="Es. Mattina" required>
            </td>
            <td class="px-4 py-3">
                <input type="time" name="slots[INDEX][start_time]" value="09:00"
                       class="px-3 py-1 border border-gray-300 rounded text-sm" required>
            </td>
            <td class="px-4 py-3">
                <input type="time" name="slots[INDEX][end_time]" value="13:00"
                       class="px-3 py-1 border border-gray-300 rounded text-sm" required>
            </td>
            <td class="px-4 py-3">
                <select name="slots[INDEX][slot_type]" 
                        class="px-3 py-1 border border-gray-300 rounded text-sm">
                    <option value="half_day">Mezza Giornata</option>
                    <option value="full_day">Giornata Intera</option>
                </select>
            </td>
            <td class="px-4 py-3">
                <div class="flex items-center gap-1">
                    <input type="number" name="slots[INDEX][price_modifier]" value="1.00"
                           class="w-20 px-2 py-1 border border-gray-300 rounded text-sm text-right"
                           step="0.01" min="0" max="10" required>
                    <span class="text-gray-500 text-sm">x</span>
                </div>
            </td>
            <td class="px-4 py-3 text-center">
                <input type="checkbox" name="slots[INDEX][is_active]" value="1" checked
                       class="w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" onclick="removeTimeSlot(this)"
                        class="text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </td>
        </tr>
    </template>
@endsection

@push('scripts')
<script>
    let slotIndex = {{ $timeSlots->count() }};

    function addTimeSlot() {
        const template = document.getElementById('timeSlotTemplate');
        const tbody = document.getElementById('timeSlotsBody');
        const clone = template.content.cloneNode(true);
        
        // Replace INDEX with actual index
        const html = clone.querySelector('tr').outerHTML.replace(/INDEX/g, slotIndex);
        tbody.insertAdjacentHTML('beforeend', html);
        
        slotIndex++;
    }

    function removeTimeSlot(button) {
        if (confirm('Sei sicuro di voler rimuovere questa fascia oraria?')) {
            button.closest('tr').remove();
            reindexSlots();
        }
    }

    function reindexSlots() {
        const rows = document.querySelectorAll('#timeSlotsBody .time-slot-row');
        rows.forEach((row, index) => {
            row.querySelectorAll('[name]').forEach(input => {
                input.name = input.name.replace(/slots\[\d+\]/, `slots[${index}]`);
            });
        });
        slotIndex = rows.length;
    }
</script>
@endpush
