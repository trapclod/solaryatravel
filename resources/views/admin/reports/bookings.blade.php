@extends('layouts.admin')

@section('title', 'Report prenotazioni')

@push('styles')
    @include('admin.reports._styles')
@endpush

@section('content')
<div class="rpt-shell">
    @include('admin.reports._sidebar', ['current' => 'bookings', 'exportType' => 'bookings'])

    <main class="rpt-main">
        <div class="rpt-header">
            <div>
                <h1>Report prenotazioni</h1>
                <p class="rpt-header-sub">
                    <i class="bi bi-calendar3"></i>
                    {{ $startDate->format('d/m/Y') }} → {{ $endDate->format('d/m/Y') }}
                </p>
            </div>
        </div>

        <div class="rpt-kpis">
            <div class="rpt-kpi is-accent-primary">
                <span class="rpt-kpi-label"><i class="bi bi-receipt"></i>Totale</span>
                <span class="rpt-kpi-value">{{ $stats['total'] }}</span>
                <span class="rpt-kpi-sub">prenotazioni</span>
            </div>
            <div class="rpt-kpi is-accent-success">
                <span class="rpt-kpi-label"><i class="bi bi-check2-circle"></i>Confermate</span>
                <span class="rpt-kpi-value">{{ $stats['confirmed'] }}</span>
                <span class="rpt-kpi-sub">+ {{ $stats['completed'] }} completate</span>
            </div>
            <div class="rpt-kpi">
                <span class="rpt-kpi-label"><i class="bi bi-people-fill"></i>Passeggeri</span>
                <span class="rpt-kpi-value">{{ $stats['passengers'] }}</span>
                <span class="rpt-kpi-sub">media {{ number_format($stats['avg_passengers'], 1, ',', '.') }}/prenot.</span>
            </div>
            <div class="rpt-kpi {{ $stats['cancellation_rate'] > 10 ? 'is-accent-danger' : '' }}">
                <span class="rpt-kpi-label"><i class="bi bi-x-circle"></i>Tasso cancellazione</span>
                <span class="rpt-kpi-value">{{ $stats['cancellation_rate'] }}%</span>
                <span class="rpt-kpi-sub">{{ $stats['cancelled'] }} cancellate</span>
            </div>
        </div>

        <div class="rpt-grid-2">
            <section class="rpt-section">
                <div class="rpt-section-head">
                    <h2 class="rpt-section-title"><i class="bi bi-clock"></i>Fasce orarie più richieste</h2>
                </div>
                @php $maxSlot = $bookingsByTimeSlot->max('count') ?? 0; @endphp
                <div class="rpt-rank">
                    @forelse($bookingsByTimeSlot as $slot)
                        @php $pct = $maxSlot > 0 ? ($slot->count / $maxSlot) * 100 : 0; @endphp
                        <div class="rpt-rank-row">
                            <span class="rpt-rank-pos"><i class="bi bi-clock"></i></span>
                            <div class="rpt-rank-body">
                                <div class="rpt-rank-line">
                                    <span class="rpt-rank-name">{{ $slot->time_slot }}</span>
                                    <span class="rpt-rank-meta">{{ $slot->count }} prenot.</span>
                                </div>
                                <div class="rpt-bar is-warning"><span style="width:{{ $pct }}%"></span></div>
                            </div>
                        </div>
                    @empty
                        <div class="rpt-empty"><i class="bi bi-clock"></i><p>Nessun dato disponibile</p></div>
                    @endforelse
                </div>
            </section>

            <section class="rpt-section">
                <div class="rpt-section-head">
                    <h2 class="rpt-section-title"><i class="bi bi-pie-chart"></i>Per stato</h2>
                </div>
                <div style="height:280px"><canvas id="statusChart"></canvas></div>
            </section>
        </div>

        <section class="rpt-section">
            <div class="rpt-section-head">
                <h2 class="rpt-section-title"><i class="bi bi-compass"></i>Per tour</h2>
            </div>
            <div class="rpt-grid-3">
                @forelse($bookingsByTour as $item)
                    <div class="rpt-tour-card">
                        <div class="rpt-tour-card-head">
                            <div>
                                <h3 class="rpt-tour-card-name">{{ $item->tour->name ?? 'Sconosciuto' }}</h3>
                                <p class="rpt-tour-card-cap">Tour</p>
                            </div>
                        </div>
                        <div class="rpt-tour-card-stats">
                            <div>
                                <div class="rpt-tour-card-stat-label">Prenot.</div>
                                <div class="rpt-tour-card-stat-value">{{ $item->total }}</div>
                            </div>
                            <div>
                                <div class="rpt-tour-card-stat-label">Pass.</div>
                                <div class="rpt-tour-card-stat-value" style="color:#1d4ed8">{{ $item->passengers }}</div>
                            </div>
                            <div>
                                <div class="rpt-tour-card-stat-label">Media</div>
                                <div class="rpt-tour-card-stat-value">{{ $item->total > 0 ? number_format($item->passengers / $item->total, 1, ',', '.') : 0 }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rpt-empty" style="grid-column:1/-1"><i class="bi bi-inbox"></i><p>Nessun dato disponibile</p></div>
                @endforelse
            </div>
        </section>

        <section class="rpt-section">
            <div class="rpt-section-head">
                <h2 class="rpt-section-title"><i class="bi bi-calendar3"></i>Dettaglio giornaliero</h2>
            </div>
            <div class="table-responsive">
                <table class="rpt-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th class="text-end">Prenotazioni</th>
                            <th class="text-end">Passeggeri</th>
                            <th class="text-end">Media</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyBookings as $day)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($day->date)->locale('it')->isoFormat('ddd D MMM YYYY') }}</td>
                                <td class="text-end">{{ $day->total }}</td>
                                <td class="text-end" style="font-weight:700;color:#1d4ed8">{{ $day->passengers }}</td>
                                <td class="text-end">{{ $day->total > 0 ? number_format($day->passengers / $day->total, 1, ',', '.') : 0 }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><div class="rpt-empty"><i class="bi bi-inbox"></i><p>Nessun dato disponibile</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
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

    const statusData = @json($bookingsByStatus);
    const statusLabels = { pending: 'In attesa', confirmed: 'Confermate', completed: 'Completate', cancelled: 'Cancellate', no_show: 'No show' };
    const statusColors = { pending: '#eab308', confirmed: '#10b981', completed: '#0284c7', cancelled: '#ef4444', no_show: '#94a3b8' };

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
            cutout: '62%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true, pointStyle: 'circle', boxWidth: 8 } },
                tooltip: { backgroundColor: '#0f172a', padding: 12, cornerRadius: 10 }
            }
        }
    });
</script>
@endpush
