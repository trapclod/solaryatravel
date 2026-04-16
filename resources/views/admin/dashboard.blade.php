@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600">Panoramica delle attività di oggi</p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Today's Bookings --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Prenotazioni Oggi</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $todayStats['bookings'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Today's Guests --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Ospiti Oggi</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $todayStats['guests'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Today's Revenue --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Incasso Oggi</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">€{{ number_format($todayStats['revenue'], 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Monthly Revenue --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Incasso Mese</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">€{{ number_format($monthlyStats['revenue'], 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts and Tables Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Revenue Chart --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Incassi Ultimi 7 Giorni</h3>
                <div class="h-64">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            {{-- Today's Bookings List --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Prenotazioni di Oggi</h3>
                    <a href="{{ route('admin.bookings.index', ['date_from' => today()->format('Y-m-d'), 'date_to' => today()->format('Y-m-d')]) }}" 
                       class="text-sm text-primary-600 hover:text-primary-700">
                        Vedi tutte
                    </a>
                </div>
                
                @if($todayBookings->count() > 0)
                    <div class="space-y-3">
                        @foreach($todayBookings->take(5) as $booking)
                            <a href="{{ route('admin.bookings.show', $booking) }}" 
                               class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-primary-600 font-semibold text-sm">
                                            {{ substr($booking->customer_first_name, 0, 1) }}{{ substr($booking->customer_last_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $booking->customer_first_name }} {{ $booking->customer_last_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $booking->catamaran->name }} - {{ $booking->timeSlot->name }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">{{ $booking->seats }} posti</p>
                                    <span @class([
                                        'text-xs px-2 py-1 rounded-full',
                                        'bg-yellow-100 text-yellow-700' => $booking->status->value === 'pending',
                                        'bg-green-100 text-green-700' => $booking->status->value === 'confirmed',
                                        'bg-blue-100 text-blue-700' => $booking->status->value === 'checked_in',
                                        'bg-gray-100 text-gray-700' => $booking->status->value === 'completed',
                                        'bg-red-100 text-red-700' => $booking->status->value === 'cancelled',
                                    ])>
                                        {{ ucfirst($booking->status->value) }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p>Nessuna prenotazione per oggi</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Pending Bookings and Activity --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Pending Bookings --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Prenotazioni in Attesa</h3>
                    <span class="bg-yellow-100 text-yellow-700 text-sm font-medium px-2 py-1 rounded-full">
                        {{ $pendingBookings->count() }}
                    </span>
                </div>
                
                @if($pendingBookings->count() > 0)
                    <div class="space-y-3">
                        @foreach($pendingBookings->take(5) as $booking)
                            <a href="{{ route('admin.bookings.show', $booking) }}" 
                               class="block p-3 rounded-lg border border-yellow-200 bg-yellow-50 hover:bg-yellow-100 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-gray-900">#{{ $booking->booking_number }}</p>
                                        <p class="text-sm text-gray-600">{{ $booking->customer_first_name }} {{ $booking->customer_last_name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900">€{{ number_format($booking->total_amount, 2) }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <p>Nessuna prenotazione in attesa</p>
                    </div>
                @endif
            </div>

            {{-- Recent Activity --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Attività Recente</h3>
                
                <div class="space-y-4">
                    @foreach($recentActivity as $activity)
                        <div class="flex items-start">
                            <div @class([
                                'w-8 h-8 rounded-full flex items-center justify-center mr-3 flex-shrink-0',
                                'bg-blue-100' => $activity['color'] === 'blue',
                                'bg-green-100' => $activity['color'] === 'green',
                                'bg-yellow-100' => $activity['color'] === 'yellow',
                                'bg-red-100' => $activity['color'] === 'red',
                            ])>
                                @if($activity['icon'] === 'calendar')
                                    <svg @class([
                                        'w-4 h-4',
                                        'text-blue-600' => $activity['color'] === 'blue',
                                        'text-green-600' => $activity['color'] === 'green',
                                    ]) fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                @elseif($activity['icon'] === 'credit-card')
                                    <svg @class([
                                        'w-4 h-4',
                                        'text-green-600' => $activity['color'] === 'green',
                                    ]) fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $activity['message'] }}</p>
                                <p class="text-xs text-gray-500">{{ $activity['details'] }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $activity['time']->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Popular Catamarans --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Catamarani Più Popolari (Questo Mese)</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach($popularCatamarans as $catamaran)
                    <div class="text-center p-4 rounded-lg bg-gray-50">
                        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                        </div>
                        <p class="font-medium text-gray-900">{{ $catamaran->name }}</p>
                        <p class="text-2xl font-bold text-primary-600">{{ $catamaran->bookings_count }}</p>
                        <p class="text-xs text-gray-500">prenotazioni</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Incasso (€)',
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: 'rgba(59, 130, 246, 0.5)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '€' + value;
                            }
                        }
                    }
                }
            }
        });
    </script>
    @endpush
@endsection
