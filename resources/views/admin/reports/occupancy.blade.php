@extends('layouts.admin')

@section('title', 'Report occupazione')

@push('styles')
    @include('admin.reports._styles')
@endpush

@php
    $avg = $stats['avg_occupancy'];
    $avgAccent = $avg >= 70 ? 'is-accent-success' : ($avg >= 50 ? 'is-accent-warning' : 'is-accent-danger');
@endphp

@section('content')
<div class="rpt-shell">
    @include('admin.reports._sidebar', ['current' => 'occupancy', 'exportType' => 'passengers'])

    <main class="rpt-main">
        <div class="rpt-header">
            <div>
                <h1>Report occupazione</h1>
                <p class="rpt-header-sub">
                    <i class="bi bi-calendar3"></i>
                    {{ $startDate->format('d/m/Y') }} → {{ $endDate->format('d/m/Y') }}
                </p>
            </div>
        </div>

        <div class="rpt-kpis">
            <div class="rpt-kpi is-accent-primary">
                <span class="rpt-kpi-label"><i class="bi bi-people-fill"></i>Trasportati</span>
                <span class="rpt-kpi-value">{{ $stats['total_capacity_used'] }}</span>
                <span class="rpt-kpi-sub">passeggeri</span>
            </div>
            <div class="rpt-kpi">
                <span class="rpt-kpi-label"><i class="bi bi-stack"></i>Capacità massima</span>
                <span class="rpt-kpi-value">{{ $stats['total_max_capacity'] }}</span>
                <span class="rpt-kpi-sub">posti disponibili</span>
            </div>
            <div class="rpt-kpi {{ $avgAccent }}">
                <span class="rpt-kpi-label"><i class="bi bi-bar-chart-fill"></i>Occupazione media</span>
                <span class="rpt-kpi-value">{{ $avg }}%</span>
                <span class="rpt-kpi-sub">tutti i tour</span>
            </div>
            <div class="rpt-kpi">
                <span class="rpt-kpi-label"><i class="bi bi-fire"></i>Giorno più affollato</span>
                <span class="rpt-kpi-value" style="font-size:1.15rem">{{ $stats['busiest_day'] }}</span>
                <span class="rpt-kpi-sub">in media</span>
            </div>
        </div>

        <section class="rpt-section">
            <div class="rpt-section-head">
                <h2 class="rpt-section-title"><i class="bi bi-compass"></i>Occupazione per tour</h2>
                <span class="rpt-section-sub">{{ count($occupancyData) }} tour attivi</span>
            </div>
            <div class="rpt-grid-3">
                @forelse($occupancyData as $item)
                    @php
                        $rate = $item['occupancy_rate'];
                        $rateClass = $rate >= 70 ? 'is-success' : ($rate >= 50 ? 'is-warning' : 'is-danger');
                        $barClass = $rate >= 70 ? 'is-success' : ($rate >= 50 ? 'is-warning' : 'is-danger');
                    @endphp
                    <div class="rpt-tour-card">
                        <div class="rpt-tour-card-head">
                            <div style="min-width:0">
                                <h3 class="rpt-tour-card-name">{{ $item['tour']->name }}</h3>
                                <p class="rpt-tour-card-cap"><i class="bi bi-people me-1"></i>Capacità {{ $item['tour']->max_capacity }} pax/slot</p>
                            </div>
                            <div style="text-align:right">
                                <div class="rpt-tour-card-rate {{ $rateClass }}">{{ $rate }}%</div>
                                <div class="rpt-tour-card-cap">occupazione</div>
                            </div>
                        </div>
                        <div class="rpt-bar {{ $barClass }}" style="height:8px"><span style="width:{{ min(100, $rate) }}%"></span></div>
                        <div class="rpt-tour-card-stats">
                            <div>
                                <div class="rpt-tour-card-stat-label">Prenot.</div>
                                <div class="rpt-tour-card-stat-value">{{ $item['bookings'] }}</div>
                            </div>
                            <div>
                                <div class="rpt-tour-card-stat-label">Pass.</div>
                                <div class="rpt-tour-card-stat-value" style="color:#1d4ed8">{{ $item['passengers'] }}</div>
                            </div>
                            <div>
                                <div class="rpt-tour-card-stat-label">Media</div>
                                <div class="rpt-tour-card-stat-value">{{ $item['avg_passengers'] }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rpt-empty" style="grid-column:1/-1"><i class="bi bi-compass"></i><p>Nessun tour attivo nel periodo</p></div>
                @endforelse
            </div>
        </section>

        <div class="rpt-grid-2">
            <section class="rpt-section">
                <div class="rpt-section-head">
                    <h2 class="rpt-section-title"><i class="bi bi-graph-up"></i>Andamento giornaliero</h2>
                </div>
                <div style="height:300px"><canvas id="dailyChart"></canvas></div>
            </section>

            <section class="rpt-section">
                <div class="rpt-section-head">
                    <h2 class="rpt-section-title"><i class="bi bi-calendar-week"></i>Per giorno settimana</h2>
                </div>
                <div style="height:300px"><canvas id="weekChart"></canvas></div>
            </section>
        </div>

        <section class="rpt-section">
            <div class="rpt-section-head">
                <h2 class="rpt-section-title"><i class="bi bi-clock"></i>Popolarità fasce orarie</h2>
            </div>
            <div class="rpt-grid-3">
                @forelse($timeSlotPopularity as $slot)
                    <div class="rpt-slot-tile">
                        <p class="rpt-slot-time"><i class="bi bi-clock me-1" style="color:#94a3b8"></i>{{ $slot->time_slot }}</p>
                        <p class="rpt-slot-count">{{ $slot->count }}</p>
                        <p class="rpt-slot-sub">prenotazioni · {{ $slot->passengers }} pax</p>
                    </div>
                @empty
                    <div class="rpt-empty" style="grid-column:1/-1"><i class="bi bi-clock"></i><p>Nessun dato disponibile</p></div>
                @endforelse
            </div>
        </section>
    </main>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.font.family = "'Google Sans', 'Inter', system-ui, sans-serif";
    Chart.defaults.color = '#64748b';

    const dailyData = @json($dailyOccupancy);
    const dailyLabels = dailyData.map(d => new Date(d.date).toLocaleDateString('it-IT', { day: '2-digit', month: '2-digit' }));
    const dailyPassengers = dailyData.map(d => d.passengers);

    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    const dailyGrad = dailyCtx.createLinearGradient(0, 0, 0, 300);
    dailyGrad.addColorStop(0, 'rgba(2, 132, 199, 0.22)');
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

    const weekData = @json($dayOfWeekStats);
    const days = ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'];
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
