@extends('layouts.admin')

@section('title', 'Report Prenotazioni')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.reports.index') }}" 
                   class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Report Prenotazioni</h1>
                    <p class="text-gray-600">Statistiche sulle prenotazioni</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('admin.reports.bookings') }}" method="GET" class="flex items-center gap-2">
                    <select name="period" onchange="this.form.submit()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Oggi</option>
                        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Questa settimana</option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Questo mese</option>
                        <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>Questo trimestre</option>
                        <option value="year" {{ $period === 'year' ? 'selected' : '' }}>Quest'anno</option>
                        <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Tutto</option>
                    </select>
                </form>
                <a href="{{ route('admin.reports.export', ['type' => 'bookings', 'period' => $period]) }}" 
                   class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                    Esporta CSV
                </a>
            </div>
        </div>

        {{-- Period Info --}}
        <div class="text-sm text-gray-500">
            Periodo: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm text-gray-500">Totale Prenotazioni</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm text-gray-500">Confermate</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['confirmed'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm text-gray-500">Passeggeri Totali</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['passengers'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm text-gray-500">Tasso Cancellazione</p>
                <p class="text-2xl font-bold {{ $stats['cancellation_rate'] > 10 ? 'text-red-600' : 'text-gray-900' }}">
                    {{ $stats['cancellation_rate'] }}%
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Bookings by Status --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Prenotazioni per Stato</h3>
                <div class="h-64">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            {{-- Bookings by Time Slot --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Prenotazioni per Fascia Oraria</h3>
                <div class="space-y-3">
                    @forelse($bookingsByTimeSlot as $slot)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-gray-700">{{ $slot->time_slot }}</span>
                                <span class="text-sm text-gray-600">{{ $slot->count }} prenotazioni</span>
                            </div>
                            <div class="w-full h-3 bg-gray-100 rounded-full overflow-hidden">
                                @php
                                    $maxSlot = $bookingsByTimeSlot->max('count');
                                    $percentage = $maxSlot > 0 ? ($slot->count / $maxSlot) * 100 : 0;
                                @endphp
                                <div class="h-full bg-blue-500 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Nessun dato disponibile</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Bookings by Catamaran --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Prenotazioni per Catamarano</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($bookingsByCatamaran as $item)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="font-semibold text-gray-900">{{ $item->catamaran->name ?? 'Sconosciuto' }}</p>
                        <div class="flex justify-between mt-2 text-sm">
                            <span class="text-gray-500">Prenotazioni</span>
                            <span class="font-medium text-gray-900">{{ $item->total }}</span>
                        </div>
                        <div class="flex justify-between mt-1 text-sm">
                            <span class="text-gray-500">Passeggeri</span>
                            <span class="font-medium text-gray-900">{{ $item->passengers }}</span>
                        </div>
                        <div class="flex justify-between mt-1 text-sm">
                            <span class="text-gray-500">Media passeggeri</span>
                            <span class="font-medium text-gray-900">{{ $item->total > 0 ? number_format($item->passengers / $item->total, 1) : 0 }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4 col-span-3">Nessun dato disponibile</p>
                @endforelse
            </div>
        </div>

        {{-- Daily Bookings Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Dettaglio Giornaliero</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Data</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Prenotazioni</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Passeggeri</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Media</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($dailyBookings as $day)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-right text-gray-600">{{ $day->total }}</td>
                                <td class="px-6 py-4 text-right text-blue-600 font-semibold">{{ $day->passengers }}</td>
                                <td class="px-6 py-4 text-right text-gray-600">
                                    {{ $day->total > 0 ? number_format($day->passengers / $day->total, 1) : 0 }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    Nessun dato disponibile per questo periodo
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const statusData = @json($bookingsByStatus);
    const statusLabels = {
        'pending': 'In attesa',
        'confirmed': 'Confermate',
        'completed': 'Completate',
        'cancelled': 'Cancellate'
    };
    
    new Chart(document.getElementById('statusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData).map(s => statusLabels[s] || s),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: [
                    'rgb(251, 191, 36)',
                    'rgb(34, 197, 94)',
                    'rgb(59, 130, 246)',
                    'rgb(239, 68, 68)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
