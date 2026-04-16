@extends('layouts.app')

@section('title', 'Le Mie Prenotazioni - Solarya Travel')

@section('content')
    <section class="py-16 lg:py-24 bg-sand-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="max-w-5xl mx-auto">
                <h1 class="text-3xl font-bold text-navy-900 mb-8">Le Mie Prenotazioni</h1>
                
                @if($bookings->isEmpty())
                    <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-600 mb-2">Nessuna prenotazione</h2>
                        <p class="text-gray-500 mb-6">Non hai ancora effettuato prenotazioni.</p>
                        <a href="{{ route('booking.start') }}" 
                           class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-colors">
                            Prenota Ora
                            <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($bookings as $booking)
                            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                                <div class="p-6">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <span class="text-sm font-mono text-gray-500">#{{ $booking->booking_number }}</span>
                                                @php
                                                    $statusColors = [
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'confirmed' => 'bg-green-100 text-green-800',
                                                        'cancelled' => 'bg-red-100 text-red-800',
                                                        'completed' => 'bg-blue-100 text-blue-800',
                                                        'no_show' => 'bg-gray-100 text-gray-800',
                                                    ];
                                                    $statusLabels = [
                                                        'pending' => 'In attesa',
                                                        'confirmed' => 'Confermata',
                                                        'cancelled' => 'Annullata',
                                                        'completed' => 'Completata',
                                                        'no_show' => 'No show',
                                                    ];
                                                @endphp
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                                                </span>
                                            </div>
                                            <h3 class="text-lg font-semibold text-navy-900 mb-1">
                                                {{ $booking->catamaran->name ?? 'Catamarano' }}
                                            </h3>
                                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    {{ $booking->booking_date->format('d/m/Y') }}
                                                </span>
                                                @if($booking->timeSlot)
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        {{ $booking->timeSlot->start_time }} - {{ $booking->timeSlot->end_time }}
                                                    </span>
                                                @endif
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                    {{ $booking->adults_count }} adulti
                                                    @if($booking->children_count > 0)
                                                        , {{ $booking->children_count }} bambini
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end gap-2">
                                            <span class="text-2xl font-bold text-primary-600">€{{ number_format($booking->total_amount, 2, ',', '.') }}</span>
                                            <a href="{{ route('booking.show', $booking->uuid) }}" 
                                               class="inline-flex items-center px-4 py-2 bg-navy-600 text-white text-sm font-semibold rounded-xl hover:bg-navy-700 transition-colors">
                                                Dettagli
                                                <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8">
                        {{ $bookings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
