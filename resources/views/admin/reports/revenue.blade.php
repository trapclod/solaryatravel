@extends('layouts.admin')

@section('title', 'Report Ricavi')

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
                    <h1 class="text-2xl font-bold text-gray-900">Report Ricavi</h1>
                    <p class="text-gray-600">Analisi dettagliata dei guadagni</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('admin.reports.revenue') }}" method="GET" class="flex items-center gap-2">
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
                <a href="{{ route('admin.reports.export', ['type' => 'revenue', 'period' => $period]) }}" 
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
                <p class="text-sm text-gray-500">Totale Ricavi</p>
                <p class="text-2xl font-bold text-green-600">€{{ number_format($stats['total'], 2, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm text-gray-500">Transazioni</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['transactions'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm text-gray-500">Media Transazione</p>
                <p class="text-2xl font-bold text-gray-900">€{{ number_format($stats['avg_transaction'], 2, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <p class="text-sm text-gray-500">Rimborsi</p>
                <p class="text-2xl font-bold text-red-600">€{{ number_format($stats['refunds'], 2, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Monthly Chart --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ricavi Mensili {{ now()->year }}</h3>
                <div class="h-64">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            {{-- Revenue by Catamaran --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ricavi per Catamarano</h3>
                <div class="space-y-3">
                    @forelse($revenueByCatamaran as $item)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">{{ $item->catamaran->name ?? 'Sconosciuto' }}</span>
                            <span class="font-semibold text-gray-900">€{{ number_format($item->total, 2, ',', '.') }}</span>
                        </div>
                        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                            @php
                                $maxRevenue = $revenueByCatamaran->max('total');
                                $percentage = $maxRevenue > 0 ? ($item->total / $maxRevenue) * 100 : 0;
                            @endphp
                            <div class="h-full bg-green-500 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Nessun dato disponibile</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Revenue by Payment Method --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ricavi per Metodo di Pagamento</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse($revenueByMethod as $method)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-500 capitalize">{{ str_replace('_', ' ', $method->payment_method ?? 'Sconosciuto') }}</p>
                        <p class="text-xl font-bold text-gray-900">€{{ number_format($method->total, 2, ',', '.') }}</p>
                        <p class="text-sm text-gray-500">{{ $method->count }} transazioni</p>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4 col-span-3">Nessun dato disponibile</p>
                @endforelse
            </div>
        </div>

        {{-- Daily Revenue Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Dettaglio Giornaliero</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Data</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Transazioni</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Totale</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Media</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($dailyRevenue as $day)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($day->date)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-right text-gray-600">{{ $day->transactions }}</td>
                                <td class="px-6 py-4 text-right font-semibold text-green-600">
                                    €{{ number_format($day->total, 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right text-gray-600">
                                    €{{ number_format($day->total / $day->transactions, 2, ',', '.') }}
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
    const monthlyData = @json($monthlyRevenue);
    const months = ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'];
    const monthlyValues = months.map((_, i) => monthlyData[i + 1] || 0);
    
    new Chart(document.getElementById('monthlyChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Ricavi (€)',
                data: monthlyValues,
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value => '€' + value.toLocaleString('it-IT')
                    }
                }
            }
        }
    });
</script>
@endpush
