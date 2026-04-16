@extends('layouts.admin')

@section('title', 'Gestione Disponibilità')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gestione Disponibilità</h1>
            <p class="text-gray-600">Seleziona un catamarano per gestire la disponibilità</p>
        </div>

        {{-- Time Slots Overview --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Fasce Orarie Attive</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @forelse($timeSlots as $slot)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="font-medium text-gray-900">{{ $slot->name }}</p>
                        <p class="text-sm text-gray-500">{{ $slot->start_time }} - {{ $slot->end_time }}</p>
                    </div>
                @empty
                    <p class="col-span-full text-gray-500">Nessuna fascia oraria configurata</p>
                @endforelse
            </div>
        </div>

        {{-- Catamarans Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($catamarans as $catamaran)
                <a href="{{ route('admin.availability.calendar', $catamaran) }}" 
                   class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow group">
                    {{-- Image --}}
                    <div class="relative aspect-video bg-gray-100">
                        @if($catamaran->images->first())
                            <img src="{{ Storage::url($catamaran->images->first()->path) }}" 
                                 alt="{{ $catamaran->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        
                        {{-- Overlay --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
                            <span class="text-white font-medium">Gestisci Disponibilità →</span>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-gray-900 text-lg">{{ $catamaran->name }}</h3>
                            @if($catamaran->is_active)
                                <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Attivo</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">Inattivo</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-4 text-sm text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $catamaran->capacity }} posti
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $catamaran->bookings_count }} prenotazioni
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Nessun catamarano</h3>
                        <p class="text-gray-500">Aggiungi prima dei catamarani per gestire la disponibilità</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
