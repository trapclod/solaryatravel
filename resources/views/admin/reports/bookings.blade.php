@extends('layouts.admin')

@section('title', 'Report prenotazioni')

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
                <h1 class="mb-0">Report prenotazioni</h1>
                <p class="mt-1 mb-0">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ $startDate->format('d/m/Y') }} → {{ $endDate->format('d/m/Y') }}
                </p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <form action="{{ route('admin.reports.bookings') }}" method="GET">
                <select name="period" onchange="this.form.submit()" class="form-select rounded-pill px-3 fw-semibold">
                    @foreach($periodLabels as $value => $label)
                        <option value="{{ $value }}" {{ $period === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.reports.export', ['type' => 'bookings', 'period' => $period]) }}"
               class="btn btn-primary rounded-pill px-3 fw-semibold">
                <i class="bi bi-download me-2"></i>Esporta CSV
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat is-active">
                <div class="dash-mini-stat-label"><i class="bi bi-receipt me-1"></i>Totale</div>
                <div class="dash-mini-stat-value">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-check2-circle me-1"></i>Confermate</div>
                <div class="dash-mini-stat-value text-success">{{ $stats['confirmed'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-people-fill me-1"></i>Passeggeri</div>
                <div class="dash-mini-stat-value text-primary">{{ $stats['passengers'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-x-circle me-1"></i>Tasso cancellazione</div>
                <div class="dash-mini-stat-value {{ $stats['cancellation_rate'] > 10 ? 'text-danger' : 'text-dark' }}">
                    {{ $stats['cancellation_rate'] }}%
                </div>
            </div>
        </div>
    </div>

    {{-- Charts row --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-5">
            <div class="dash-card h-100">
                <div class="dash-card-header">
                    <h3><i class="bi bi-pie-chart me-2 text-primary"></i>Per stato</h3>
                </div>
                <div class="dash-card-body">
                    <div style="height:320px"><canvas id="statusChart"></canvas></div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="dash-card h-100">
                <div class="dash-card-header">
                    <h3><i class="bi bi-clock me-2 text-warning"></i>Per fascia oraria</h3>
                </div>
                <div class="dash-card-body">
                    @php $maxSlot = $bookingsByTimeSlot->max('count') ?? 0; @endphp
                    @forelse($bookingsByTimeSlot as $slot)
                        @php $pct = $maxSlot > 0 ? ($slot->count / $maxSlot) * 100 : 0; @endphp
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="small fw-semibold text-dark">
                                    <i class="bi bi-clock me-1 text-warning"></i>{{ $slot->time_slot }}
                                </span>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                    {{ $slot->count }} prenotazioni
                                </span>
                            </div>
                            <div class="progress" style="height:10px; border-radius:999px">
                                <div class="progress-bar bg-warning" style="width: {{ $pct }}%; border-radius:999px"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-clock fs-1 d-block mb-2 opacity-50"></i>
                            <p class="mb-0">Nessun dato disponibile</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- By catamaran --}}
    <div class="dash-card mb-3">
        <div class="dash-card-header">
            <h3><i class="bi bi-water me-2 text-primary"></i>Per catamarano</h3>
        </div>
        <div class="dash-card-body">
            <div class="row g-3">
                @forelse($bookingsByCatamaran as $item)
                    <div class="col-md-6 col-lg-4">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="rounded-3 bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center"
                                      style="width:36px; height:36px">
                                    <i class="bi bi-water"></i>
                                </span>
                                <span class="fw-semibold text-dark text-truncate">{{ $item->catamaran->name ?? 'Sconosciuto' }}</span>
                            </div>
                            <div class="row g-2 small mt-2">
                                <div class="col-4">
                                    <div class="text-muted">Prenot.</div>
                                    <div class="fw-bold text-dark">{{ $item->total }}</div>
                                </div>
                                <div class="col-4">
                                    <div class="text-muted">Pass.</div>
                                    <div class="fw-bold text-primary">{{ $item->passengers }}</div>
                                </div>
                                <div class="col-4">
                                    <div class="text-muted">Media</div>
                                    <div class="fw-bold text-dark">{{ $item->total > 0 ? number_format($item->passengers / $item->total, 1, ',', '.') : 0 }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
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
                        <th class="text-end">Prenotazioni</th>
                        <th class="text-end">Passeggeri</th>
                        <th class="text-end">Media</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dailyBookings as $day)
                        <tr>
                            <td class="fw-semibold text-dark">
                                <i class="bi bi-calendar-event me-2 text-muted"></i>
                                {{ \Carbon\Carbon::parse($day->date)->locale('it')->isoFormat('ddd D MMM YYYY') }}
                            </td>
                            <td class="text-end">
                                <span class="badge bg-light text-dark border">{{ $day->total }}</span>
                            </td>
                            <td class="text-end fw-bold text-primary">{{ $day->passengers }}</td>
                            <td class="text-end text-muted">{{ $day->total > 0 ? number_format($day->passengers / $day->total, 1, ',', '.') : 0 }}</td>
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
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true, pointStyle: 'circle' } },
                tooltip: { backgroundColor: '#0f172a', padding: 12, cornerRadius: 10 }
            }
        }
    });
</script>
@endpush
