@extends('layouts.admin')

@section('title', 'Report ricavi')

@php
    $periodLabels = [
        'today' => 'Oggi', 'week' => 'Questa settimana', 'month' => 'Questo mese',
        'quarter' => 'Questo trimestre', 'year' => "Quest'anno", 'all' => 'Tutto lo storico',
    ];
@endphp

@section('content')
    <div class="dash-page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.reports.index') }}" class="dash-icon-btn" title="Torna ai report">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="mb-0">Report ricavi</h1>
                <p class="mt-1 mb-0">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ $startDate->format('d/m/Y') }} → {{ $endDate->format('d/m/Y') }}
                </p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <form action="{{ route('admin.reports.revenue') }}" method="GET">
                <select name="period" onchange="this.form.submit()" class="form-select rounded-pill px-3 fw-semibold">
                    @foreach($periodLabels as $value => $label)
                        <option value="{{ $value }}" {{ $period === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.reports.export', ['type' => 'revenue', 'period' => $period]) }}"
               class="btn btn-primary rounded-pill px-3 fw-semibold">
                <i class="bi bi-download me-2"></i>Esporta CSV
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat is-active">
                <div class="dash-mini-stat-label"><i class="bi bi-cash-coin me-1"></i>Totale ricavi</div>
                <div class="dash-mini-stat-value text-success">€{{ number_format($stats['total'], 2, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-receipt me-1"></i>Transazioni</div>
                <div class="dash-mini-stat-value">{{ $stats['transactions'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-graph-up-arrow me-1"></i>Media transazione</div>
                <div class="dash-mini-stat-value">€{{ number_format($stats['avg_transaction'], 2, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-arrow-counterclockwise me-1"></i>Rimborsi</div>
                <div class="dash-mini-stat-value text-danger">€{{ number_format($stats['refunds'], 2, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-7">
            <div class="dash-card h-100">
                <div class="dash-card-header">
                    <h3><i class="bi bi-bar-chart me-2 text-success"></i>Ricavi mensili {{ now()->year }}</h3>
                </div>
                <div class="dash-card-body">
                    <div style="height:340px"><canvas id="monthlyChart"></canvas></div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="dash-card h-100">
                <div class="dash-card-header">
                    <h3><i class="bi bi-water me-2 text-primary"></i>Per catamarano</h3>
                </div>
                <div class="dash-card-body">
                    @php $maxRevenue = $revenueByCatamaran->max('total') ?? 0; @endphp
                    @forelse($revenueByCatamaran as $item)
                        @php $pct = $maxRevenue > 0 ? ($item->total / $maxRevenue) * 100 : 0; @endphp
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="small text-secondary text-truncate me-2">
                                    <i class="bi bi-water me-1"></i>{{ $item->catamaran->name ?? 'Sconosciuto' }}
                                </span>
                                <span class="fw-bold text-success">€{{ number_format($item->total, 2, ',', '.') }}</span>
                            </div>
                            <div class="progress" style="height:8px; border-radius:999px">
                                <div class="progress-bar bg-success" style="width: {{ $pct }}%; border-radius:999px"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                            <p class="mb-0">Nessun dato disponibile</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Payment methods --}}
    <div class="dash-card mb-3">
        <div class="dash-card-header">
            <h3><i class="bi bi-credit-card me-2 text-primary"></i>Per metodo di pagamento</h3>
        </div>
        <div class="dash-card-body">
            <div class="row g-3">
                @forelse($revenueByMethod as $method)
                    @php
                        $methodKey = $method->payment_method ?? 'sconosciuto';
                        $methodIcon = match($methodKey) {
                            'stripe', 'card', 'credit_card' => 'bi-credit-card-2-front',
                            'paypal' => 'bi-paypal',
                            'bank_transfer', 'transfer' => 'bi-bank',
                            'cash' => 'bi-cash-stack',
                            default => 'bi-wallet2',
                        };
                    @endphp
                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center"
                                      style="width:36px; height:36px">
                                    <i class="bi {{ $methodIcon }}"></i>
                                </span>
                                <span class="small text-muted text-capitalize">{{ str_replace('_', ' ', $methodKey) }}</span>
                            </div>
                            <div class="fs-4 fw-bold text-dark">€{{ number_format($method->total, 2, ',', '.') }}</div>
                            <div class="small text-muted">{{ $method->count }} transazioni</div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">
                        <i class="bi bi-credit-card fs-1 d-block mb-2 opacity-50"></i>
                        <p class="mb-0">Nessun dato disponibile</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Daily detail --}}
    <div class="dash-card mb-4">
        <div class="dash-card-header">
            <h3><i class="bi bi-calendar3 me-2 text-primary"></i>Dettaglio giornaliero</h3>
        </div>
        <div class="table-responsive">
            <table class="dash-table mb-0">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th class="text-end">Transazioni</th>
                        <th class="text-end">Totale</th>
                        <th class="text-end">Media</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailyRevenue as $day)
                        <tr>
                            <td class="fw-semibold text-dark">
                                <i class="bi bi-calendar-event me-2 text-muted"></i>
                                {{ \Carbon\Carbon::parse($day->date)->locale('it')->isoFormat('ddd D MMM YYYY') }}
                            </td>
                            <td class="text-end">
                                <span class="badge bg-light text-dark border">{{ $day->transactions }}</span>
                            </td>
                            <td class="text-end fw-bold text-success">€{{ number_format($day->total, 2, ',', '.') }}</td>
                            <td class="text-end text-muted">€{{ number_format($day->total / max($day->transactions, 1), 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                    <p class="mb-0">Nessun dato disponibile per questo periodo</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.family = "'Google Sans', 'Inter', system-ui, sans-serif";
    Chart.defaults.color = '#64748b';

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
                backgroundColor: 'rgba(16, 185, 129, 0.85)',
                hoverBackgroundColor: '#10b981',
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: { label: (ctx) => '€' + ctx.parsed.y.toLocaleString('it-IT', { minimumFractionDigits: 2 }) }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(15,23,42,0.05)' },
                    ticks: { callback: v => '€' + v.toLocaleString('it-IT') }
                },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endpush
