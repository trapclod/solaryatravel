@extends('layouts.admin')

@section('title', $catamaran->name)

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.catamarans.index') }}" 
                   class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $catamaran->name }}</h1>
                        @if($catamaran->is_active)
                            <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Attivo</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">Inattivo</span>
                        @endif
                    </div>
                    <p class="text-gray-600">{{ $catamaran->description_short }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.availability.calendar', $catamaran) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Disponibilità
                </a>
                <a href="{{ route('admin.catamarans.edit', $catamaran) }}" 
                   class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifica
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Images --}}
                @if($catamaran->images->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="aspect-video">
                            <img src="{{ Storage::url($catamaran->images->first()->path) }}" 
                                 alt="{{ $catamaran->name }}"
                                 class="w-full h-full object-cover">
                        </div>
                        @if($catamaran->images->count() > 1)
                            <div class="p-4 grid grid-cols-4 gap-2">
                                @foreach($catamaran->images->skip(1)->take(4) as $image)
                                    <img src="{{ Storage::url($image->path) }}" 
                                         alt="{{ $catamaran->name }}"
                                         class="aspect-video object-cover rounded-lg">
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Description --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Descrizione</h2>
                    <div class="prose prose-sm max-w-none text-gray-600">
                        {!! nl2br(e($catamaran->description)) !!}
                    </div>
                </div>

                {{-- Features --}}
                @php
                    $features = $catamaran->features;
                    if (is_string($features)) {
                        $features = json_decode($features, true) ?? [];
                    }
                    $features = is_array($features) ? $features : [];
                @endphp
                @if(count($features) > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Caratteristiche</h2>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach($features as $feature)
                                <div class="flex items-center gap-2 text-gray-600">
                                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $feature }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Recent Bookings --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Prenotazioni Recenti</h2>
                        <a href="{{ route('admin.bookings.index') }}?catamaran={{ $catamaran->id }}" 
                           class="text-sm text-primary-600 hover:text-primary-700">
                            Vedi tutte
                        </a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($catamaran->bookings as $booking)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div>
                                    <a href="{{ route('admin.bookings.show', $booking) }}" 
                                       class="font-medium text-gray-900 hover:text-primary-600">
                                        #{{ $booking->booking_number }}
                                    </a>
                                    <p class="text-sm text-gray-500">
                                        {{ $booking->customer_first_name }} {{ $booking->customer_last_name }} • 
                                        {{ $booking->booking_date->format('d/m/Y') }}
                                    </p>
                                </div>
                                @php
                                    $statusValue = $booking->status instanceof \App\Enums\BookingStatus ? $booking->status->value : $booking->status;
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'confirmed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'completed' => 'bg-blue-100 text-blue-800',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$statusValue] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($statusValue) }}
                                </span>
                            </div>
                        @empty
                            <div class="px-6 py-8 text-center text-gray-500">
                                Nessuna prenotazione recente
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Stats --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Statistiche</h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Prenotazioni totali</span>
                            <span class="font-semibold text-gray-900">{{ $stats['total_bookings'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Prossime prenotazioni</span>
                            <span class="font-semibold text-gray-900">{{ $stats['upcoming_bookings'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Ricavi totali</span>
                            <span class="font-semibold text-green-600">€{{ number_format($stats['total_revenue'], 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Specifications --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Specifiche</h2>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Capacità</span>
                            <span class="font-medium text-gray-900">{{ $catamaran->capacity }} persone</span>
                        </div>
                        @if($catamaran->length_meters)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Lunghezza</span>
                                <span class="font-medium text-gray-900">{{ $catamaran->length_meters }} metri</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Pricing --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Prezzi</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Mezza Giornata</p>
                            <p class="text-xl font-bold text-gray-900">€{{ number_format($catamaran->base_price_half_day, 0, ',', '.') }}</p>
                            @if($catamaran->exclusive_price_half_day)
                                <p class="text-sm text-gray-500">Esclusivo: €{{ number_format($catamaran->exclusive_price_half_day, 0, ',', '.') }}</p>
                            @endif
                        </div>
                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-sm text-gray-500 mb-1">Giornata Intera</p>
                            <p class="text-xl font-bold text-gray-900">€{{ number_format($catamaran->base_price_full_day, 0, ',', '.') }}</p>
                            @if($catamaran->exclusive_price_full_day)
                                <p class="text-sm text-gray-500">Esclusivo: €{{ number_format($catamaran->exclusive_price_full_day, 0, ',', '.') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Azioni Rapide</h2>
                    <div class="space-y-2">
                        <form action="{{ route('admin.catamarans.toggle', $catamaran) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 text-left text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2 {{ $catamaran->is_active ? 'text-yellow-600' : 'text-green-600' }}">
                                @if($catamaran->is_active)
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                    Disattiva Catamarano
                                @else
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Attiva Catamarano
                                @endif
                            </button>
                        </form>
                        <a href="{{ route('catamarans.show', $catamaran) }}" 
                           target="_blank"
                           class="w-full px-4 py-2 text-left text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Vedi sul Sito
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
