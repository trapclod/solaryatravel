@extends('layouts.admin')

@section('title', 'Report e statistiche')

@php
    $periodLabels = [
        'today' => 'Oggi',
        'week' => 'Questa settimana',
        'month' => 'Questo mese',
        'quarter' => 'Questo trimestre',
        'year' => "Quest'anno",
        'all' => 'Tutto lo storico',
    ];
@endphp

@section('content')
    {{-- Page header --}}
    <div class="dash-page-header">
        <div>
            <h1>Report e statistiche</h1>
            <p>
                <i class="bi bi-calendar3 me-1"></i>
                {{ $periodLabels[$period] ?? 'Periodo' }}
                <span class="text-muted">·</span>
                {{ $startDate->format('d/m/Y') }} → {{ $endDate->format('d/m/Y') }}
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <form action="{{ route('admin.reports.index') }}" method="GET">
                <select name="period" onchange="this.form.submit()" class="form-select rounded-pill px-3 fw-semibold">
                    @foreach($periodLabels as $value => $label)
                        <option value="{{ $value }}" {{ $period === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.reports.export', ['period' => $period]) }}"
               class="btn btn-light border rounded-pill px-3 fw-semibold">
                <i class="bi bi-download me-2"></i>Esporta CSV
            </a>
        </div>
    </div>

    {{-- Quick KPI stats --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="dash-stat" style="background: linear-gradient(135deg, #10b981, #059669)">
                <div class="dash-stat-icon"><i class="bi bi-cash-coin"></i></div>
                <div class="dash-stat-label">Ricavi</div>
                <div class="dash-stat-value">€{{ number_format($revenue, 0, ',', '.') }}</div>
                @if($previousRevenue > 0)
                    @php $change = (($revenue - $previousRevenue) / $previousRevenue) * 100; @endphp
                    <div class="dash-stat-trend">
                        <i class="bi {{ $change >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }}"></i>
                        {{ number_format(abs($change), 1, ',', '.') }}% vs precedente
                    </div>
                @endif
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="dash-stat" style="background: linear-gradient(135deg, #0284c7, #0369a1)">
                <div class="dash-stat-icon"><i class="bi bi-receipt"></i></div>
                <div class="dash-stat-label">Prenotazioni</div>
                <div class="dash-stat-value">{{ $totalBookings }}</div>
                <div class="dash-stat-trend"><i class="bi bi-check2-circle"></i>{{ $confirmedBookings }} confermate</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="dash-stat" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9)">
                <div class="dash-stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="dash-stat-label">Passeggeri</div>
                <div class="dash-stat-value">{{ $totalPassengers }}</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="dash-stat" style="background: linear-gradient(135deg, #eab308, #ca8a04)">
                <div class="dash-stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="dash-stat-label">Valore medio</div>
                <div class="dash-stat-value">€{{ number_format($avgBookingValue, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Report navigation cards --}}
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <a href="{{ route('admin.reports.revenue', ['period' => $period]) }}"
               class="dash-card h-100 d-block text-decoration-none cat-card-link"
               style="border-color:rgba(16,185,129,.25)">
                <div class="dash-card-body">
                    <div class="d-flex align-items-start gap-3">
                        <span class="rounded-3 bg-success-subtle text-success d-inline-flex align-items-center justify-content-center flex-shrink-0"
                              style="width:48px; height:48px; font-size:1.5rem">
                            <i class="bi bi-cash-coin"></i>
                        </span>
                        <div class="flex-grow-1 min-w-0">
                            <h3 class="fw-bold text-dark mb-1">Report ricavi</h3>
                            <p class="small text-muted mb-2">Analisi dettagliata di guadagni, transazioni e metodi di pagamento.</p>
                            <span class="small fw-semibold text-success">
                                Apri report <i class="bi bi-arrow-right ms-1"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('admin.reports.bookings', ['period' => $period]) }}"
               class="dash-card h-100 d-block text-decoration-none cat-card-link"
               style="border-color:rgba(2,132,199,.25)">
                <div class="dash-card-body">
                    <div class="d-flex align-items-start gap-3">
                        <span class="rounded-3 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center flex-shrink-0"
                              style="width:48px; height:48px; font-size:1.5rem">
                            <i class="bi bi-receipt"></i>
                        </span>
                        <div class="flex-grow-1 min-w-0">
                            <h3 class="fw-bold text-dark mb-1">Report prenotazioni</h3>
                            <p class="small text-muted mb-2">Statistiche per stato, fascia oraria e catamarano.</p>
                            <span class="small fw-semibold text-primary">
                                Apri report <i class="bi bi-arrow-right ms-1"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('admin.reports.occupancy', ['period' => $period]) }}"
               class="dash-card h-100 d-block text-decoration-none cat-card-link"
               style="border-color:rgba(139,92,246,.25)">
                <div class="dash-card-body">
                    <div class="d-flex align-items-start gap-3">
                        <span class="rounded-3 d-inline-flex align-items-center justify-content-center flex-shrink-0"
                              style="width:48px; height:48px; font-size:1.5rem; background:rgba(139,92,246,.12); color:#8b5cf6">
                            <i class="bi bi-bar-chart-fill"></i>
                        </span>
                        <div class="flex-grow-1 min-w-0">
                            <h3 class="fw-bold text-dark mb-1">Report occupazione</h3>
                            <p class="small text-muted mb-2">Tasso di riempimento dei catamarani e fasce più popolari.</p>
                            <span class="small fw-semibold" style="color:#8b5cf6">
                                Apri report <i class="bi bi-arrow-right ms-1"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="dash-card h-100">
                <div class="dash-card-header">
                    <h3><i class="bi bi-graph-up me-2 text-success"></i>Andamento ricavi</h3>
                </div>
                <div class="dash-card-body">
                    <div style="height:320px"><canvas id="revenueChart"></canvas></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="dash-card h-100">
                <div class="dash-card-header">
                    <h3><i class="bi bi-pie-chart me-2 text-primary"></i>Per stato</h3>
                </div>
                <div class="dash-card-body">
                    <div style="height:320px"><canvas id="statusChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top catamarans --}}
    <div class="dash-card mb-4">
        <div class="dash-card-header">
            <h3><i class="bi bi-trophy me-2 text-warning"></i>Catamarani più prenotati</h3>
        </div>
        <div class="dash-card-body">
            @php $maxBookings = $topCatamarans->max('bookings_count') ?? 0; @endphp
            @forelse($topCatamarans as $index => $catamaran)
                @php
                    $pct = $maxBookings > 0 ? ($catamaran->bookings_count / $maxBookings) * 100 : 0;
                    $medal = ['🥇', '🥈', '🥉'][$index] ?? '';
                @endphp
                <div class="d-flex align-items-center gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <span class="rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                          style="width:36px; height:36px">
                        {{ $medal ?: $index + 1 }}
                    </span>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-semibold text-dark text-truncate">{{ $catamaran->name }}</span>
                            <span class="small text-muted ms-2">{{ $catamaran->bookings_count }} prenotazioni</span>
                        </div>
                        <div class="progress" style="height:8px; border-radius:999px">
                            <div class="progress-bar bg-primary" style="width: {{ $pct }}%; border-radius:999px"></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-bar-chart fs-1 d-block mb-2 opacity-50"></i>
                    <p class="mb-0">Nessun dato disponibile per questo periodo</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.family = "'Google Sans', 'Inter', system-ui, sans-serif";
    Chart.defaults.color = '#64748b';

    // Revenue chart
    const revenueData = @json($revenueByDay);
    const revenueLabels = Object.keys(revenueData).map(date => {
        return new Date(date).toLocaleDateString('it-IT', { day: '2-digit', month: 'short' });
    });
    const revenueValues = Object.values(revenueData);

    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revenueCtx.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Ricavi (€)',
                data: revenueValues,
                borderColor: '#10b981',
                backgroundColor: gradient,
                borderWidth: 2.5,
                fill: true,
                tension: 0.35,
                pointRadius: 3,
                pointBackgroundColor: '#10b981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
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
                    callbacks: {
                        label: (ctx) => '€' + ctx.parsed.y.toLocaleString('it-IT', { minimumFractionDigits: 2 })
                    }
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

    // Status chart
    const statusData = @json($bookingsByStatus);
    const statusLabels = { pending: 'In attesa', confirmed: 'Confermate', completed: 'Completate', cancelled: 'Cancellate' };
    const statusColors = { pending: '#eab308', confirmed: '#10b981', completed: '#0284c7', cancelled: '#ef4444' };

    new Chart(document.getElementById('statusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(statusData).map(s => statusLabels[s] || s),
            datasets: [{
                data: Object.values(statusData),
                backgroundColor: Object.keys(statusData).map(s => statusColors[s] || '#94a3b8'),
                borderWidth: 3,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true, pointStyle: 'circle' } },
                tooltip: { backgroundColor: '#0f172a', padding: 12, cornerRadius: 10 }
            }
        }
    });
</script>
@endpush
