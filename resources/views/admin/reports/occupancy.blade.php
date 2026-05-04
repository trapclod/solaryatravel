@extends('layouts.admin')

@section('title', 'Report occupazione')

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
                <h1 class="mb-0">Report occupazione</h1>
                <p class="mt-1 mb-0">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ $startDate->format('d/m/Y') }} → {{ $endDate->format('d/m/Y') }}
                </p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <form action="{{ route('admin.reports.occupancy') }}" method="GET">
                <select name="period" onchange="this.form.submit()" class="form-select rounded-pill px-3 fw-semibold">
                    @foreach($periodLabels as $value => $label)
                        <option value="{{ $value }}" {{ $period === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.reports.export', ['type' => 'passengers', 'period' => $period]) }}"
               class="btn btn-primary rounded-pill px-3 fw-semibold">
                <i class="bi bi-download me-2"></i>Esporta CSV
            </a>
        </div>
    </div>

    {{-- Stats --}}
    @php
        $avg = $stats['avg_occupancy'];
        $avgClass = $avg >= 70 ? 'text-success' : ($avg >= 50 ? 'text-warning' : 'text-danger');
    @endphp
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat is-active">
                <div class="dash-mini-stat-label"><i class="bi bi-people-fill me-1"></i>Trasportati</div>
                <div class="dash-mini-stat-value">{{ $stats['total_capacity_used'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-stack me-1"></i>Capacità massima</div>
                <div class="dash-mini-stat-value text-muted">{{ $stats['total_max_capacity'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-bar-chart-fill me-1"></i>Occupazione media</div>
                <div class="dash-mini-stat-value {{ $avgClass }}">{{ $avg }}%</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-fire me-1"></i>Giorno più affollato</div>
                <div class="dash-mini-stat-value" style="font-size:1.05rem">{{ $stats['busiest_day'] }}</div>
            </div>
        </div>
    </div>

    {{-- Occupancy by catamaran --}}
    <div class="dash-card mb-3">
        <div class="dash-card-header">
            <h3><i class="bi bi-water me-2 text-primary"></i>Occupazione per catamarano</h3>
        </div>
        <div class="dash-card-body">
            @forelse($occupancyData as $item)
                @php
                    $rate = $item['occupancy_rate'];
                    $rateClass = $rate >= 70 ? 'success' : ($rate >= 50 ? 'warning' : 'danger');
                @endphp
                <div class="border rounded-3 p-3 mb-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <div class="d-flex align-items-center gap-3">
                            <span class="rounded-3 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center"
                                  style="width:42px; height:42px; font-size:1.25rem">
                                <i class="bi bi-water"></i>
                            </span>
                            <div>
                                <h4 class="fw-bold text-dark mb-0">{{ $item['catamaran']->name }}</h4>
                                <p class="small text-muted mb-0">
                                    <i class="bi bi-people me-1"></i>Capacità {{ $item['catamaran']->max_capacity }} persone
                                </p>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="display-6 fw-bold text-{{ $rateClass }}">{{ $rate }}%</span>
                            <p class="small text-muted mb-0">occupazione</p>
                        </div>
                    </div>
                    <div class="progress mb-3" style="height:12px; border-radius:999px">
                        <div class="progress-bar bg-{{ $rateClass }}" style="width: {{ min(100, $rate) }}%; border-radius:999px"></div>
                    </div>
                    <div class="row g-3 small">
                        <div class="col-4">
                            <div class="text-muted"><i class="bi bi-receipt me-1"></i>Prenotazioni</div>
                            <div class="fw-bold text-dark fs-6">{{ $item['bookings'] }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted"><i class="bi bi-people me-1"></i>Passeggeri</div>
                            <div class="fw-bold text-primary fs-6">{{ $item['passengers'] }}</div>
                        </div>
                        <div class="col-4">
                            <div class="text-muted"><i class="bi bi-graph-up me-1"></i>Media a viaggio</div>
                            <div class="fw-bold text-dark fs-6">{{ $item['avg_passengers'] }}</div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-water fs-1 d-block mb-2 opacity-50"></i>
                    <p class="mb-0">Nessun dato disponibile</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Charts row --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-7">
            <div class="dash-card h-100">
                <div class="dash-card-header">
                    <h3><i class="bi bi-graph-up me-2 text-primary"></i>Andamento giornaliero</h3>
                </div>
                <div class="dash-card-body">
                    <div style="height:300px"><canvas id="dailyChart"></canvas></div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="dash-card h-100">
                <div class="dash-card-header">
                    <h3><i class="bi bi-calendar-week me-2 text-primary"></i>Distribuzione per giorno</h3>
                </div>
                <div class="dash-card-body">
                    <div style="height:300px"><canvas id="weekChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Time slot popularity --}}
    <div class="dash-card mb-4">
        <div class="dash-card-header">
            <h3><i class="bi bi-clock me-2 text-warning"></i>Popolarità fasce orarie</h3>
        </div>
        <div class="dash-card-body">
            <div class="row g-3">
                @forelse($timeSlotPopularity as $slot)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="border rounded-3 p-3 text-center h-100"
                             style="background: linear-gradient(135deg, rgba(234,179,8,.06), rgba(2,132,199,.06))">
                            <span class="rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center mb-2"
                                  style="width:44px; height:44px; font-size:1.25rem">
                                <i class="bi bi-clock"></i>
                            </span>
                            <p class="fs-6 fw-semibold text-dark mb-1">{{ $slot->time_slot }}</p>
                            <p class="display-6 fw-bold text-primary mb-1">{{ $slot->count }}</p>
                            <p class="small text-muted mb-0">prenotazioni</p>
                            <p class="small text-secondary mb-0">
                                <i class="bi bi-people me-1"></i>{{ $slot->passengers }} passeggeri
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">
                        <i class="bi bi-clock fs-1 d-block mb-2 opacity-50"></i>
                        <p class="mb-0">Nessun dato disponibile</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.family = "'Google Sans', 'Inter', system-ui, sans-serif";
    Chart.defaults.color = '#64748b';

    // Daily chart
    const dailyData = @json($dailyOccupancy);
    const dailyLabels = dailyData.map(d => new Date(d.date).toLocaleDateString('it-IT', { day: '2-digit', month: '2-digit' }));
    const dailyPassengers = dailyData.map(d => d.passengers);

    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    const dailyGrad = dailyCtx.createLinearGradient(0, 0, 0, 300);
    dailyGrad.addColorStop(0, 'rgba(2, 132, 199, 0.25)');
    dailyGrad.addColorStop(1, 'rgba(2, 132, 199, 0.0)');

    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: dailyLabels,
            datasets: [{
                label: 'Passeggeri',
                data: dailyPassengers,
                borderColor: '#0284c7',
                backgroundColor: dailyGrad,
                borderWidth: 2.5,
                fill: true,
                tension: 0.35,
                pointRadius: 3,
                pointBackgroundColor: '#0284c7',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#0f172a', padding: 12, cornerRadius: 10 }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(15,23,42,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Week chart
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
                backgroundColor: 'rgba(2, 132, 199, 0.85)',
                hoverBackgroundColor: '#0284c7',
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#0f172a', padding: 12, cornerRadius: 10 }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(15,23,42,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endpush
