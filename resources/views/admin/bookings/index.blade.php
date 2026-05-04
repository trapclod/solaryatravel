@extends('layouts.admin')

@section('title', 'Prenotazioni')

@section('content')
    @php
        $statusMeta = [
            'pending'    => ['label' => 'In attesa',  'icon' => 'bi-hourglass-split', 'color' => 'warning'],
            'confirmed'  => ['label' => 'Confermata', 'icon' => 'bi-check-circle',   'color' => 'success'],
            'checked_in' => ['label' => 'Check-in',   'icon' => 'bi-qr-code-scan',   'color' => 'info'],
            'completed'  => ['label' => 'Completata', 'icon' => 'bi-flag-fill',      'color' => 'secondary'],
            'cancelled'  => ['label' => 'Annullata',  'icon' => 'bi-x-circle',       'color' => 'danger'],
            'no_show'    => ['label' => 'No show',    'icon' => 'bi-eye-slash',      'color' => 'secondary'],
        ];
        $currentStatus = request('status');
        $hasFilters = request()->hasAny(['search', 'status', 'catamaran', 'date_from', 'date_to']);
    @endphp

    {{-- Page header --}}
    <div class="dash-page-header">
        <div>
            <h1>Prenotazioni</h1>
            <p>Gestisci, filtra e monitora tutte le prenotazioni dei tuoi clienti.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if($hasFilters)
                <a href="{{ route('admin.bookings.index') }}" class="btn btn-light rounded-pill px-3 fw-semibold border">
                    <i class="bi bi-x-lg me-2"></i>Reset filtri
                </a>
            @endif
            <a href="{{ route('booking.start') }}" class="btn btn-primary rounded-pill px-3 fw-semibold" target="_blank">
                <i class="bi bi-plus-lg me-2"></i>Nuova prenotazione
            </a>
        </div>
    </div>

    {{-- Mini stats / quick filters --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-4 col-xl">
            <a href="{{ route('admin.bookings.index') }}" class="dash-mini-stat text-decoration-none {{ !$currentStatus ? 'is-active' : '' }}">
                <span class="mini-stat-icon bg-primary-subtle text-primary"><i class="bi bi-collection"></i></span>
                <div>
                    <div class="mini-stat-value">{{ $bookings->total() }}</div>
                    <div class="mini-stat-label">Totale</div>
                </div>
            </a>
        </div>
        @foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $st)
            @php $m = $statusMeta[$st]; @endphp
            <div class="col-6 col-md-4 col-xl">
                <a href="{{ route('admin.bookings.index', array_merge(request()->except('status', 'page'), ['status' => $st])) }}"
                   class="dash-mini-stat text-decoration-none {{ $currentStatus === $st ? 'is-active' : '' }}">
                    <span class="mini-stat-icon bg-{{ $m['color'] }}-subtle text-{{ $m['color'] }}">
                        <i class="bi {{ $m['icon'] }}"></i>
                    </span>
                    <div>
                        <div class="mini-stat-value">{{ $stats[$st] ?? 0 }}</div>
                        <div class="mini-stat-label">{{ $m['label'] }}</div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="dash-filter-bar">
        <form action="{{ route('admin.bookings.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-6 col-xl-4">
                <label for="search" class="form-label">Cerca</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           placeholder="Nome, email, numero..." class="form-control">
                </div>
            </div>
            <div class="col-md-6 col-xl-2">
                <label for="status" class="form-label">Stato</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Tutti</option>
                    @foreach ($statusMeta as $val => $m)
                        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $m['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 col-xl-2">
                <label for="catamaran" class="form-label">Catamarano</label>
                <select name="catamaran" id="catamaran" class="form-select">
                    <option value="">Tutti</option>
                    @foreach($catamarans as $catamaran)
                        <option value="{{ $catamaran->id }}" {{ request('catamaran') == $catamaran->id ? 'selected' : '' }}>
                            {{ $catamaran->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 col-xl-2">
                <label for="date_from" class="form-label">Da</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control">
            </div>
            <div class="col-md-6 col-xl-2">
                <label for="date_to" class="form-label">A</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control">
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                @if($hasFilters)
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-light rounded-pill px-3 border">Reset</a>
                @endif
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                    <i class="bi bi-funnel me-2"></i>Applica filtri
                </button>
            </div>
        </form>
    </div>

    {{-- Bookings table --}}
    <div class="dash-card">
        <div class="dash-card-header">
            <h3>
                <i class="bi bi-list-ul me-2 text-primary"></i>
                Elenco prenotazioni
                <span class="ms-2 badge bg-light text-secondary fw-medium">{{ $bookings->total() }}</span>
            </h3>
            <div class="d-flex align-items-center gap-2 small text-muted">
                <i class="bi bi-info-circle"></i>
                Pagina {{ $bookings->currentPage() }} di {{ max(1, $bookings->lastPage()) }}
            </div>
        </div>

        <div class="table-responsive">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Prenotazione</th>
                        <th>Cliente</th>
                        <th>Catamarano</th>
                        <th>Data</th>
                        <th class="text-center">Ospiti</th>
                        <th class="text-end">Totale</th>
                        <th>Stato</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        @php
                            $sv = $booking->status instanceof \App\Enums\BookingStatus ? $booking->status->value : $booking->status;
                            $m = $statusMeta[$sv] ?? ['label' => ucfirst($sv), 'icon' => 'bi-circle', 'color' => 'secondary'];
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="font-monospace fw-semibold text-primary text-decoration-none">
                                    #{{ $booking->booking_number }}
                                </a>
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-clock me-1"></i>{{ $booking->created_at->format('d/m/Y H:i') }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar-sm bg-primary-subtle text-primary" style="font-size:.75rem">
                                        {{ strtoupper(substr($booking->customer_first_name, 0, 1) . substr($booking->customer_last_name, 0, 1)) }}
                                    </span>
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-truncate" style="max-width:200px">
                                            {{ $booking->customer_first_name }} {{ $booking->customer_last_name }}
                                        </div>
                                        <div class="small text-muted text-truncate" style="max-width:200px">
                                            <i class="bi bi-envelope me-1"></i>{{ $booking->customer_email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-water text-primary"></i>
                                    <span>{{ $booking->catamaran->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $booking->booking_date->format('d/m/Y') }}</div>
                                @if($booking->timeSlot)
                                    <div class="small text-muted">
                                        <i class="bi bi-clock me-1"></i>{{ $booking->timeSlot->start_time }} – {{ $booking->timeSlot->end_time }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="d-inline-flex align-items-center justify-content-center bg-light rounded-pill px-3 py-1 fw-semibold small">
                                    <i class="bi bi-people me-1 text-muted"></i>{{ $booking->seats }}
                                </span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold">€{{ number_format($booking->total_amount, 2, ',', '.') }}</span>
                            </td>
                            <td>
                                <span class="status-pill s-{{ $sv }}">
                                    <i class="bi {{ $m['icon'] }}"></i>{{ $m['label'] }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('admin.bookings.show', $booking) }}"
                                       class="dash-icon-btn is-primary" title="Visualizza" data-bs-toggle="tooltip">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($sv === 'pending')
                                        <form action="{{ route('admin.bookings.confirm', $booking) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dash-icon-btn is-success" title="Conferma" data-bs-toggle="tooltip">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if(!in_array($sv, ['cancelled', 'completed']))
                                        <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Sei sicuro di voler annullare questa prenotazione?')">
                                            @csrf
                                            <button type="submit" class="dash-icon-btn is-danger" title="Annulla" data-bs-toggle="tooltip">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-journal-x display-4 opacity-50 d-block mb-3"></i>
                                    <p class="fw-semibold mb-1">Nessuna prenotazione trovata</p>
                                    <p class="small mb-3">Prova a modificare i filtri di ricerca.</p>
                                    @if($hasFilters)
                                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Rimuovi filtri
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 px-3 py-3 border-top small text-muted">
                <div>
                    Mostrando <strong>{{ $bookings->firstItem() }}–{{ $bookings->lastItem() }}</strong>
                    di <strong>{{ $bookings->total() }}</strong>
                </div>
                <div>
                    {{ $bookings->withQueryString()->onEachSide(1)->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));
    });
</script>
@endpush
