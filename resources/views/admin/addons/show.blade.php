@extends('layouts.admin')

@section('title', $addon->name)

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.addons.index') }}" 
                   class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $addon->name }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        @if($addon->is_active)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                Attivo
                            </span>
                        @else
                            <span class="inline-flex px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">
                                Inattivo
                            </span>
                        @endif
                        <span class="text-gray-400">•</span>
                        <span class="text-sm text-gray-500">{{ $addon->slug }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('admin.addons.toggle', $addon) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        {{ $addon->is_active ? 'Disattiva' : 'Attiva' }}
                    </button>
                </form>
                <a href="{{ route('admin.addons.edit', $addon) }}" 
                   class="px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    Modifica
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Details Card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Dettagli</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-start gap-4">
                            @if($addon->image_path)
                                <img src="{{ Storage::url($addon->image_path) }}" 
                                     alt="{{ $addon->name }}"
                                     class="w-24 h-24 rounded-lg object-cover">
                            @else
                                <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                @if($addon->description)
                                    <p class="text-gray-600">{{ $addon->description }}</p>
                                @else
                                    <p class="text-gray-400 italic">Nessuna descrizione</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-6 pt-6 border-t border-gray-100">
                        <div>
                            <p class="text-sm text-gray-500">Prezzo</p>
                            <p class="text-lg font-semibold text-gray-900">€{{ number_format($addon->price, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tipo Prezzo</p>
                            @php
                                $priceTypes = [
                                    'per_person' => 'Per persona',
                                    'per_booking' => 'Per prenotazione',
                                    'per_unit' => 'Per unità',
                                ];
                            @endphp
                            <p class="text-lg font-semibold text-gray-900">{{ $priceTypes[$addon->price_type] ?? $addon->price_type }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Quantità Max</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $addon->max_quantity ?? 'Illimitata' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Preavviso</p>
                            <p class="text-lg font-semibold text-gray-900">
                                @if($addon->requires_advance_booking)
                                    {{ $addon->advance_hours }}h
                                @else
                                    No
                                @endif
                            </p>
                        </div>
                    </div>
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
                                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600 uppercase">Quantità</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 uppercase">Totale</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($recentBookings as $booking)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <a href="{{ route('admin.bookings.show', $booking) }}" class="text-primary-600 hover:underline font-medium">
                                                    {{ $booking->booking_number }}
                                                </a>
                                                <p class="text-xs text-gray-500">{{ $booking->booking_date->format('d/m/Y') }}</p>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                {{ $booking->catamaran->name ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-center text-sm">
                                                {{ $booking->pivot->quantity }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-medium">
                                                €{{ number_format($booking->pivot->total_price, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-6">Nessuna prenotazione con questo extra</p>
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
                            <span class="text-gray-600">Prenotazioni totali</span>
                            <span class="font-semibold text-gray-900">{{ $stats['total_bookings'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Ricavo totale</span>
                            <span class="font-semibold text-gray-900">€{{ number_format($stats['total_revenue'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Quantità media</span>
                            <span class="font-semibold text-gray-900">{{ number_format($stats['avg_quantity'], 1, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Info --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informazioni</h3>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Creato il</span>
                            <span class="text-gray-900">{{ $addon->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Ultimo aggiornamento</span>
                            <span class="text-gray-900">{{ $addon->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Ordine</span>
                            <span class="text-gray-900">{{ $addon->sort_order }}</span>
                        </div>
                    </div>
                </div>

                {{-- Danger Zone --}}
                <div class="bg-red-50 rounded-xl border border-red-200 p-6">
                    <h3 class="text-lg font-semibold text-red-800 mb-4">Zona Pericolosa</h3>
                    
                    <p class="text-sm text-red-700 mb-4">
                        L'eliminazione dell'extra è permanente. Se ci sono prenotazioni associate, non sarà possibile eliminarlo.
                    </p>

                    <form action="{{ route('admin.addons.destroy', $addon) }}" 
                          method="POST"
                          onsubmit="return confirm('Sei sicuro di voler eliminare questo extra? Questa azione è irreversibile.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                            Elimina Extra
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
