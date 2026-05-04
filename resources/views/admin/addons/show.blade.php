@extends('layouts.admin')

@section('title', $addon->name)

@php
    $priceTypes = [
        'per_person' => ['label' => 'Per persona', 'icon' => 'bi-person'],
        'per_booking' => ['label' => 'Per prenotazione', 'icon' => 'bi-receipt'],
        'per_unit' => ['label' => 'Per unità', 'icon' => 'bi-box-seam'],
    ];
    $pt = $priceTypes[$addon->price_type] ?? ['label' => $addon->price_type, 'icon' => 'bi-tag'];
@endphp

@section('content')
    {{-- Page header --}}
    <div class="dash-page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.addons.index') }}" class="dash-icon-btn" title="Torna agli extra">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h1 class="mb-0">{{ $addon->name }}</h1>
                    @if($addon->is_active)
                        <span class="status-pill s-confirmed"><i class="bi bi-check-circle-fill"></i>Attivo</span>
                    @else
                        <span class="status-pill s-cancelled"><i class="bi bi-pause-circle-fill"></i>Inattivo</span>
                    @endif
                </div>
                <p class="mt-1 mb-0">
                    <code class="bg-light text-muted px-2 py-1 rounded small">{{ $addon->slug }}</code>
                </p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <form action="{{ route('admin.addons.toggle', $addon) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-light rounded-pill px-3 fw-semibold border">
                    <i class="bi {{ $addon->is_active ? 'bi-pause-circle' : 'bi-play-circle' }} me-2"></i>
                    {{ $addon->is_active ? 'Disattiva' : 'Attiva' }}
                </button>
            </form>
            <a href="{{ route('admin.addons.edit', $addon) }}" class="btn btn-primary rounded-pill px-3 fw-semibold">
                <i class="bi bi-pencil-square me-2"></i>Modifica
            </a>
        </div>
    </div>

    <div class="row g-3">
        {{-- LEFT --}}
        <div class="col-lg-8">
            {{-- Hero detail --}}
            <div class="dash-card mb-3">
                <div class="dash-card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                            @if($addon->image_path)
                                <div class="ratio ratio-1x1 rounded-3 overflow-hidden">
                                    <img src="{{ Storage::url($addon->image_path) }}" alt="{{ $addon->name }}"
                                         class="w-100 h-100" style="object-fit:cover">
                                </div>
                            @else
                                <div class="ratio ratio-1x1 rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center">
                                    <i class="bi bi-stars" style="font-size:4rem"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            @if($addon->description)
                                <p class="text-secondary lh-lg mb-3">{{ $addon->description }}</p>
                            @else
                                <p class="text-muted fst-italic mb-3">Nessuna descrizione disponibile.</p>
                            @endif

                            <div class="d-flex align-items-baseline gap-2 mb-3">
                                <span class="display-6 fw-bold text-dark">€{{ number_format($addon->price, 2, ',', '.') }}</span>
                                <span class="small text-muted"><i class="bi {{ $pt['icon'] }} me-1"></i>{{ $pt['label'] }}</span>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-2">
                                    <i class="bi bi-stack me-1"></i>
                                    Max: {{ $addon->max_quantity ?? 'Illimitata' }}
                                </span>
                                @if($addon->requires_advance_booking)
                                    <span class="badge bg-warning-subtle text-warning border-0 rounded-pill px-3 py-2">
                                        <i class="bi bi-clock-history me-1"></i>
                                        Prenota con {{ $addon->advance_hours }}h di anticipo
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent bookings --}}
            <div class="dash-card mb-3">
                <div class="dash-card-header">
                    <h3><i class="bi bi-clock-history me-2 text-primary"></i>Prenotazioni recenti</h3>
                    @if($recentBookings->count() > 0)
                        <span class="small text-muted">{{ $recentBookings->count() }} risultati</span>
                    @endif
                </div>

                @if($recentBookings->count() > 0)
                    <div class="table-responsive">
                        <table class="dash-table mb-0">
                            <thead>
                                <tr>
                                    <th>Prenotazione</th>
                                    <th>Catamarano</th>
                                    <th class="text-center">Quantità</th>
                                    <th class="text-end">Totale</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings as $booking)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.bookings.show', $booking) }}"
                                               class="fw-semibold text-primary text-decoration-none">
                                                #{{ $booking->booking_number }}
                                            </a>
                                            <div class="small text-muted">
                                                {{ \Carbon\Carbon::parse($booking->booking_date)->locale('it')->isoFormat('D MMM YYYY') }}
                                            </div>
                                        </td>
                                        <td class="small text-muted">{{ $booking->catamaran->name ?? '—' }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border">{{ $booking->pivot->quantity }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            €{{ number_format($booking->pivot->total_price, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="dash-card-body text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                        <p class="mb-0">Nessuna prenotazione con questo extra</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT --}}
        <div class="col-lg-4">
            <div class="dash-card mb-3">
                <div class="dash-card-header">
                    <h3><i class="bi bi-bar-chart me-2 text-primary"></i>Statistiche</h3>
                </div>
                <div class="dash-card-body">
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <span class="small text-muted"><i class="bi bi-journal-bookmark me-2"></i>Prenotazioni totali</span>
                        <span class="fw-bold">{{ $stats['total_bookings'] }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <span class="small text-muted"><i class="bi bi-cash-coin me-2"></i>Ricavo totale</span>
                        <span class="fw-bold text-success">€{{ number_format($stats['total_revenue'], 2, ',', '.') }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2">
                        <span class="small text-muted"><i class="bi bi-calculator me-2"></i>Quantità media</span>
                        <span class="fw-bold">{{ number_format($stats['avg_quantity'], 1, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="dash-card mb-3">
                <div class="dash-card-header">
                    <h3><i class="bi bi-info-circle me-2 text-primary"></i>Informazioni</h3>
                </div>
                <div class="dash-card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom small">
                        <span class="text-muted"><i class="bi bi-calendar-plus me-2"></i>Creato</span>
                        <span class="text-dark">{{ $addon->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom small">
                        <span class="text-muted"><i class="bi bi-calendar-check me-2"></i>Aggiornato</span>
                        <span class="text-dark">{{ $addon->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 small">
                        <span class="text-muted"><i class="bi bi-sort-numeric-down me-2"></i>Ordine</span>
                        <span class="text-dark">{{ $addon->sort_order }}</span>
                    </div>
                </div>
            </div>

            {{-- Danger zone --}}
            <div class="dash-card mb-3 border-danger" style="border-color:rgba(220,53,69,.25) !important">
                <div class="dash-card-header" style="background:rgba(220,53,69,.05)">
                    <h3 class="text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Zona pericolosa</h3>
                </div>
                <div class="dash-card-body">
                    <p class="small text-muted mb-3">
                        L'eliminazione è permanente. Se ci sono prenotazioni associate, l'operazione fallirà.
                    </p>
                    <button type="button" class="btn btn-outline-danger w-100 rounded-pill fw-semibold"
                            data-bs-toggle="modal" data-bs-target="#deleteAddonModal">
                        <i class="bi bi-trash me-2"></i>Elimina extra
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete modal --}}
    <div class="modal fade" id="deleteAddonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:1rem">
                <div class="modal-body text-center p-4">
                    <div class="mx-auto mb-3 rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center" style="width:72px;height:72px">
                        <i class="bi bi-exclamation-triangle fs-2"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Eliminare {{ $addon->name }}?</h4>
                    <p class="text-muted">L'azione è irreversibile.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal">Annulla</button>
                    <form action="{{ route('admin.addons.destroy', $addon) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">
                            <i class="bi bi-trash me-2"></i>Elimina definitivamente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
