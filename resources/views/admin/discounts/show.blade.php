@extends('layouts.admin')

@section('title', $discount->code)

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.discounts.index') }}" 
                   class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 font-mono">{{ $discount->code }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        @php
                            $isExpired = $discount->valid_until && $discount->valid_until->isPast();
                            $isUpcoming = $discount->valid_from && $discount->valid_from->isFuture();
                            $isExhausted = $discount->usage_limit && $discount->usage_count >= $discount->usage_limit;
                        @endphp
                        
                        @if(!$discount->is_active)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">
                                Inattivo
                            </span>
                        @elseif($isExpired)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">
                                Scaduto
                            </span>
                        @elseif($isExhausted)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold bg-orange-100 text-orange-800 rounded-full">
                                Esaurito
                            </span>
                        @elseif($isUpcoming)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
                                Futuro
                            </span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                Attivo
                            </span>
                        @endif
                        
                        @if($discount->description)
                            <span class="text-gray-400">•</span>
                            <span class="text-sm text-gray-500">{{ $discount->description }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('admin.discounts.toggle', $discount) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        {{ $discount->is_active ? 'Disattiva' : 'Attiva' }}
                    </button>
                </form>
                <a href="{{ route('admin.discounts.edit', $discount) }}" 
                   class="px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    Modifica
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Discount Details --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Dettagli Sconto</h2>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="col-span-2 md:col-span-1">
                            <p class="text-sm text-gray-500">Sconto</p>
                            <p class="text-2xl font-bold text-primary-600">
                                @if($discount->discount_type === 'percentage')
                                    {{ $discount->discount_value }}%
                                @else
                                    €{{ number_format($discount->discount_value, 2, ',', '.') }}
                                @endif
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $discount->discount_type === 'percentage' ? 'Percentuale' : 'Fisso' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Minimo Ordine</p>
                            <p class="text-lg font-semibold text-gray-900">
                                @if($discount->min_amount)
                                    €{{ number_format($discount->min_amount, 2, ',', '.') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Sconto Max</p>
                            <p class="text-lg font-semibold text-gray-900">
                                @if($discount->max_discount)
                                    €{{ number_format($discount->max_discount, 2, ',', '.') }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Per Utente</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $discount->user_limit }}x</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mt-6 pt-6 border-t border-gray-100">
                        <div>
                            <p class="text-sm text-gray-500">Valido Da</p>
                            <p class="text-lg font-semibold text-gray-900">
                                @if($discount->valid_from)
                                    {{ $discount->valid_from->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-gray-400">Sempre</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Valido Fino A</p>
                            <p class="text-lg font-semibold {{ $isExpired ? 'text-red-600' : 'text-gray-900' }}">
                                @if($discount->valid_until)
                                    {{ $discount->valid_until->format('d/m/Y H:i') }}
                                    @if($isExpired)
                                        <span class="text-sm text-red-500">(scaduto)</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">Senza scadenza</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Usage Progress --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Utilizzi</h2>
                    
                    <div class="flex items-center gap-4 mb-4">
                        <div class="text-4xl font-bold text-gray-900">{{ $discount->usage_count }}</div>
                        @if($discount->usage_limit)
                            <div class="text-gray-400 text-2xl">/</div>
                            <div class="text-2xl text-gray-500">{{ $discount->usage_limit }}</div>
                        @else
                            <div class="text-sm text-gray-500">utilizzi (illimitati)</div>
                        @endif
                    </div>

                    @if($discount->usage_limit)
                        <div class="w-full h-4 bg-gray-100 rounded-full overflow-hidden">
                            @php
                                $percentage = min(100, ($discount->usage_count / $discount->usage_limit) * 100);
                                $color = $percentage >= 100 ? 'bg-red-500' : ($percentage >= 80 ? 'bg-orange-500' : 'bg-primary-600');
                            @endphp
                            <div class="{{ $color }} h-full rounded-full transition-all" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            {{ number_format($percentage, 1) }}% utilizzato
                            @if($isExhausted)
                                - <span class="text-red-600 font-medium">Esaurito</span>
                            @endif
                        </p>
                    @endif
                </div>

                {{-- Recent Bookings --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Prenotazioni Recenti</h2>
                    
                    @if($recentBookings->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Prenotazione</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Catamarano</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase">Data</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 uppercase">Sconto</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($recentBookings as $booking)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <a href="{{ route('admin.bookings.show', $booking) }}" class="text-primary-600 hover:underline font-medium">
                                                    {{ $booking->booking_number }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                {{ $booking->catamaran->name ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                {{ $booking->booking_date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-medium text-green-600">
                                                -€{{ number_format($booking->discount_amount ?? 0, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-6">Nessuna prenotazione con questo codice</p>
                    @endif
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Stats --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiche</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Prenotazioni</span>
                            <span class="font-semibold text-gray-900">{{ $stats['total_bookings'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Sconto Totale</span>
                            <span class="font-semibold text-green-600">€{{ number_format($stats['total_discount'], 2, ',', '.') }}</span>
                        </div>
                        @if($stats['usage_rate'] !== null)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Tasso Utilizzo</span>
                                <span class="font-semibold text-gray-900">{{ $stats['usage_rate'] }}%</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Copy Code --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Copia Codice</h3>
                    
                    <div class="flex gap-2">
                        <input type="text" 
                               value="{{ $discount->code }}" 
                               id="discount-code"
                               readonly
                               class="flex-1 px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg font-mono text-center">
                        <button onclick="copyCode()" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Info --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informazioni</h3>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Creato il</span>
                            <span class="text-gray-900">{{ $discount->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Ultimo aggiornamento</span>
                            <span class="text-gray-900">{{ $discount->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Danger Zone --}}
                <div class="bg-red-50 rounded-xl border border-red-200 p-6">
                    <h3 class="text-lg font-semibold text-red-800 mb-4">Zona Pericolosa</h3>
                    
                    <p class="text-sm text-red-700 mb-4">
                        L'eliminazione è permanente. Se ci sono prenotazioni associate, non sarà possibile eliminare il codice.
                    </p>

                    <form action="{{ route('admin.discounts.destroy', $discount) }}" 
                          method="POST"
                          onsubmit="return confirm('Sei sicuro di voler eliminare questo codice sconto? Questa azione è irreversibile.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                            Elimina Codice
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function copyCode() {
        const input = document.getElementById('discount-code');
        input.select();
        document.execCommand('copy');
        
        // Optional: show feedback
        const btn = event.currentTarget;
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
        setTimeout(() => {
            btn.innerHTML = originalHTML;
        }, 2000);
    }
</script>
@endpush
