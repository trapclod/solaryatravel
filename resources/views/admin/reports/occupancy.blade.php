@extends('layouts.admin')

@section('title', 'Report Occupazione')

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
                    <h1 class="text-2xl font-bold text-gray-900">Report Occupazione</h1>
                    <p class="text-gray-600">Tasso di occupazione dei catamarani</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('admin.reports.occupancy') }}" method="GET" class="flex items-center gap-2">
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
                <a href="{{ route('admin.reports.export', ['type' => 'passengers', 'period' => $period]) }}" 
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
                <p class="text-sm text-gray-500">Passeggeri Trasportati</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_capacity_used'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm text-gray-500">Capacità Massima</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_max_capacity'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm text-gray-500">Occupazione Media</p>
                <p class="text-2xl font-bold {{ $stats['avg_occupancy'] >= 70 ? 'text-green-600' : ($stats['avg_occupancy'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ $stats['avg_occupancy'] }}%
                </p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm text-gray-500">Giorno più Affollato</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['busiest_day'] }}</p>
            </div>
        </div>

        {{-- Occupancy by Catamaran --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Occupazione per Catamarano</h3>
            <div class="space-y-4">
                @forelse($occupancyData as $item)
                    <div class="border border-gray-100 rounded-lg p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $item['catamaran']->name }}</h4>
                                <p class="text-sm text-gray-500">Capacità: {{ $item['catamaran']->max_capacity }} persone</p>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold {{ $item['occupancy_rate'] >= 70 ? 'text-green-600' : ($item['occupancy_rate'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $item['occupancy_rate'] }}%
                                </span>
                                <p class="text-sm text-gray-500">occupazione</p>
                            </div>
                        </div>
                        <div class="w-full h-4 bg-gray-100 rounded-full overflow-hidden mb-3">
                            @php
                                $color = $item['occupancy_rate'] >= 70 ? 'bg-green-500' : ($item['occupancy_rate'] >= 50 ? 'bg-yellow-500' : 'bg-red-500');
                            @endphp
                            <div class="{{ $color }} h-full rounded-full transition-all" style="width: {{ min(100, $item['occupancy_rate']) }}%"></div>
                        </div>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Prenotazioni</span>
                                <p class="font-semibold text-gray-900">{{ $item['bookings'] }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Passeggeri</span>
                                <p class="font-semibold text-gray-900">{{ $item['passengers'] }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Media per viaggio</span>
                                <p class="font-semibold text-gray-900">{{ $item['avg_passengers'] }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Nessun dato disponibile</p>
                @endforelse
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Daily Trend --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Andamento Giornaliero</h3>
                <div class="h-64">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>

            {{-- Day of Week --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribuzione per Giorno</h3>
                <div class="h-64">
                    <canvas id="weekChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Time Slot Popularity --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Popolarità Fasce Orarie</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse($timeSlotPopularity as $slot)
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-lg font-semibold text-gray-900">{{ $slot->time_slot }}</p>
                        <p class="text-3xl font-bold text-primary-600 my-2">{{ $slot->count }}</p>
                        <p class="text-sm text-gray-500">prenotazioni</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $slot->passengers }} passeggeri</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4 col-span-4">Nessun dato disponibile</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Daily Chart
    const dailyData = @json($dailyOccupancy);
    const dailyLabels = dailyData.map(d => {
        const date = new Date(d.date);
        return date.toLocaleDateString('it-IT', { day: '2-digit', month: '2-digit' });
    });
    const dailyPassengers = dailyData.map(d => d.passengers);
    
    new Chart(document.getElementById('dailyChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [{
                label: 'Passeggeri',
                data: dailyPassengers,
                borderColor: 'rgb(147, 51, 234)',
                backgroundColor: 'rgba(147, 51, 234, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Week Chart
    const weekData = @json($dayOfWeekStats);
    const days = ['Dom', 'Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab'];
    const weekValues = [1,2,3,4,5,6,7].map(d => weekData[d] || 0);
    
    new Chart(document.getElementById('weekChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: days,
            datasets: [{
                label: 'Prenotazioni',
                data: weekValues,
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endpush
