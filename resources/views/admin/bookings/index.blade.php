@extends('layouts.admin')

@section('title', 'Prenotazioni')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Prenotazioni</h1>
                <p class="text-gray-600">Gestisci tutte le prenotazioni</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <form action="{{ route('admin.bookings.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cerca</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           placeholder="Nome, email, numero..."
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Stato</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Tutti</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>In attesa</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confermata</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completata</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annullata</option>
                        <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No show</option>
                    </select>
                </div>
                <div>
                    <label for="catamaran" class="block text-sm font-medium text-gray-700 mb-1">Catamarano</label>
                    <select name="catamaran" id="catamaran" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Tutti</option>
                        @foreach($catamarans as $catamaran)
                            <option value="{{ $catamaran->id }}" {{ request('catamaran') == $catamaran->id ? 'selected' : '' }}>
                                {{ $catamaran->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Data da</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                           class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Data a</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                               class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                        Filtra
                    </button>
                </div>
            </form>
        </div>

        {{-- Stats Summary --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <p class="text-2xl font-bold text-gray-900">{{ $bookings->total() }}</p>
                <p class="text-sm text-gray-500">Totale</p>
            </div>
            <div class="bg-yellow-50 rounded-xl border border-yellow-200 p-4 text-center">
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</p>
                <p class="text-sm text-yellow-700">In attesa</p>
            </div>
            <div class="bg-green-50 rounded-xl border border-green-200 p-4 text-center">
                <p class="text-2xl font-bold text-green-600">{{ $stats['confirmed'] ?? 0 }}</p>
                <p class="text-sm text-green-700">Confermate</p>
            </div>
            <div class="bg-blue-50 rounded-xl border border-blue-200 p-4 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ $stats['completed'] ?? 0 }}</p>
                <p class="text-sm text-blue-700">Completate</p>
            </div>
            <div class="bg-red-50 rounded-xl border border-red-200 p-4 text-center">
                <p class="text-2xl font-bold text-red-600">{{ $stats['cancelled'] ?? 0 }}</p>
                <p class="text-sm text-red-700">Annullate</p>
            </div>
        </div>

        {{-- Bookings Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Prenotazione
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Catamarano
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Data
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Ospiti
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Totale
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Stato
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Azioni
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($bookings as $booking)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" class="font-mono text-sm text-primary-600 hover:text-primary-700">
                                        #{{ $booking->booking_number }}
                                    </a>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $booking->created_at->format('d/m/Y H:i') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900">{{ $booking->customer_first_name }} {{ $booking->customer_last_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $booking->customer_email }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-900">{{ $booking->catamaran->name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-900">{{ $booking->booking_date->format('d/m/Y') }}</p>
                                    @if($booking->timeSlot)
                                        <p class="text-sm text-gray-500">{{ $booking->timeSlot->start_time }} - {{ $booking->timeSlot->end_time }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full text-sm font-medium text-gray-700">
                                        {{ $booking->seats }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-gray-900">€{{ number_format($booking->total_amount, 2, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusValue = $booking->status instanceof \App\Enums\BookingStatus ? $booking->status->value : $booking->status;
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
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$statusValue] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$statusValue] ?? ucfirst($statusValue) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.bookings.show', $booking) }}" 
                                           class="p-2 text-gray-400 hover:text-primary-600 transition-colors"
                                           title="Visualizza">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        @if($statusValue === 'pending')
                                            <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="p-2 text-gray-400 hover:text-green-600 transition-colors"
                                                        title="Conferma">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                        @if(!in_array($statusValue, ['cancelled', 'completed']))
                                            <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Sei sicuro di voler annullare questa prenotazione?')">
                                                @csrf
                                                <button type="submit" 
                                                        class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                                                        title="Annulla">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="text-gray-500">Nessuna prenotazione trovata</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($bookings->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $bookings->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
