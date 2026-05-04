@extends('layouts.admin')

@section('title', $discount->code)

@php
    $isExpired = $discount->valid_until && $discount->valid_until->isPast();
    $isUpcoming = $discount->valid_from && $discount->valid_from->isFuture();
    $isExhausted = $discount->usage_limit && $discount->usage_count >= $discount->usage_limit;

    if (!$discount->is_active) { $st = 'inactive'; $stLabel = 'Inattivo'; $stIcon = 'bi-pause-circle-fill'; }
    elseif ($isExpired) { $st = 'cancelled'; $stLabel = 'Scaduto'; $stIcon = 'bi-x-circle-fill'; }
    elseif ($isExhausted) { $st = 'no_show'; $stLabel = 'Esaurito'; $stIcon = 'bi-exclamation-circle-fill'; }
    elseif ($isUpcoming) { $st = 'pending'; $stLabel = 'Futuro'; $stIcon = 'bi-hourglass-split'; }
    else { $st = 'confirmed'; $stLabel = 'Attivo'; $stIcon = 'bi-check-circle-fill'; }
@endphp

@section('content')
    {{-- Header --}}
    <div class="dash-page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.discounts.index') }}" class="dash-icon-btn" title="Torna ai codici sconto">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h1 class="mb-0 font-monospace">{{ $discount->code }}</h1>
                    <span class="status-pill s-{{ $st }}"><i class="bi {{ $stIcon }}"></i>{{ $stLabel }}</span>
                </div>
                @if($discount->description)
                    <p class="mt-1 mb-0">{{ $discount->description }}</p>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <form action="{{ route('admin.discounts.toggle', $discount) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-light border rounded-pill px-3 fw-semibold">
                    <i class="bi {{ $discount->is_active ? 'bi-pause-circle' : 'bi-play-circle' }} me-2"></i>
                    {{ $discount->is_active ? 'Disattiva' : 'Attiva' }}
                </button>
            </form>
            <a href="{{ route('admin.discounts.edit', $discount) }}" class="btn btn-primary rounded-pill px-3 fw-semibold">
                <i class="bi bi-pencil-square me-2"></i>Modifica
            </a>
        </div>
    </div>

    <div class="row g-3">
        {{-- LEFT --}}
        <div class="col-lg-8">
            {{-- Hero card with discount value --}}
            <div class="dash-card mb-3"
                 style="background: linear-gradient(135deg, rgba(2,132,199,.06), rgba(234,179,8,.06)); border: 1px solid rgba(2,132,199,.2)">
                <div class="dash-card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-5 text-center">
                            <div class="text-uppercase small fw-semibold text-muted mb-2">Valore sconto</div>
                            <div class="display-3 fw-bold text-primary lh-1">
                                @if($discount->discount_type === 'percentage')
                                    {{ rtrim(rtrim(number_format($discount->discount_value, 2, ',', ''), '0'), ',') }}<span class="display-5">%</span>
                                @else
                                    €{{ number_format($discount->discount_value, 2, ',', '.') }}
                                @endif
                            </div>
                            <div class="small text-muted mt-2">
                                <i class="bi {{ $discount->discount_type === 'percentage' ? 'bi-percent' : 'bi-currency-euro' }} me-1"></i>
                                {{ $discount->discount_type === 'percentage' ? 'Percentuale sull\'ordine' : 'Importo fisso' }}
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="bg-white rounded-3 border p-3 h-100">
                                        <div class="small text-muted mb-1"><i class="bi bi-arrow-down-circle me-1"></i>Min. ordine</div>
                                        <div class="fw-bold text-dark">
                                            @if($discount->min_amount)
                                                €{{ number_format($discount->min_amount, 2, ',', '.') }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-white rounded-3 border p-3 h-100">
                                        <div class="small text-muted mb-1"><i class="bi bi-arrow-up-circle me-1"></i>Sconto max</div>
                                        <div class="fw-bold text-dark">
                                            @if($discount->max_discount)
                                                €{{ number_format($discount->max_discount, 2, ',', '.') }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-white rounded-3 border p-3 h-100">
                                        <div class="small text-muted mb-1"><i class="bi bi-people me-1"></i>Per utente</div>
                                        <div class="fw-bold text-dark">{{ $discount->user_limit }}x</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-white rounded-3 border p-3 h-100">
                                        <div class="small text-muted mb-1"><i class="bi bi-stack me-1"></i>Limite totale</div>
                                        <div class="fw-bold text-dark">{{ $discount->usage_limit ?? '∞' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Validity period --}}
            <div class="dash-card mb-3">
                <div class="dash-card-header">
                    <h3><i class="bi bi-calendar-range me-2 text-primary"></i>Validità</h3>
                </div>
                <div class="dash-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2">
                                <span class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                      style="width:42px; height:42px">
                                    <i class="bi bi-play-circle-fill"></i>
                                </span>
                                <div>
                                    <div class="small text-muted">Valido da</div>
                                    <div class="fw-bold text-dark">
                                        @if($discount->valid_from)
                                            {{ $discount->valid_from->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-muted">Sempre</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-2">
                                <span class="rounded-circle {{ $isExpired ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning' }} d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                      style="width:42px; height:42px">
                                    <i class="bi bi-stop-circle-fill"></i>
                                </span>
                                <div>
                                    <div class="small text-muted">Valido fino a</div>
                                    <div class="fw-bold {{ $isExpired ? 'text-danger' : 'text-dark' }}">
                                        @if($discount->valid_until)
                                            {{ $discount->valid_until->format('d/m/Y H:i') }}
                                            @if($isExpired) <small>(scaduto)</small> @endif
                                        @else
                                            <span class="text-muted">Senza scadenza</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Usage progress --}}
            <div class="dash-card mb-3">
                <div class="dash-card-header">
                    <h3><i class="bi bi-graph-up me-2 text-primary"></i>Utilizzi</h3>
                </div>
                <div class="dash-card-body">
                    <div class="d-flex align-items-baseline gap-2 mb-3">
                        <div class="display-5 fw-bold text-dark">{{ $discount->usage_count }}</div>
                        @if($discount->usage_limit)
                            <div class="h4 text-muted mb-0">/ {{ $discount->usage_limit }}</div>
                        @else
                            <div class="small text-muted ms-2">utilizzi (illimitati)</div>
                        @endif
                    </div>

                    @if($discount->usage_limit)
                        @php $pct = min(100, ($discount->usage_count / $discount->usage_limit) * 100); @endphp
                        <div class="progress" style="height:14px; border-radius:999px">
                            <div class="progress-bar {{ $pct >= 100 ? 'bg-danger' : ($pct >= 80 ? 'bg-warning' : 'bg-primary') }}"
                                 style="width: {{ $pct }}%; border-radius:999px">
                                {{ number_format($pct, 0) }}%
                            </div>
                        </div>
                        <div class="small text-muted mt-2">
                            {{ number_format($pct, 1, ',', '.') }}% utilizzato
                            @if($isExhausted)
                                — <span class="text-danger fw-bold"><i class="bi bi-exclamation-circle me-1"></i>Esaurito</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recent bookings --}}
            <div class="dash-card mb-3">
                <div class="dash-card-header">
                    <h3><i class="bi bi-receipt me-2 text-primary"></i>Prenotazioni recenti</h3>
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
                                    <th>Data</th>
                                    <th class="text-end">Sconto applicato</th>
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
                                        </td>
                                        <td class="small text-muted">{{ $booking->catamaran->name ?? '—' }}</td>
                                        <td class="small text-muted">{{ $booking->booking_date->format('d/m/Y') }}</td>
                                        <td class="text-end fw-bold text-success">
                                            -€{{ number_format($booking->discount_amount ?? 0, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="dash-card-body text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                        <p class="mb-0">Nessuna prenotazione con questo codice</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT SIDEBAR --}}
        <div class="col-lg-4">
            {{-- Copy code --}}
            <div class="dash-card mb-3">
                <div class="dash-card-header">
                    <h3><i class="bi bi-clipboard me-2 text-primary"></i>Copia codice</h3>
                </div>
                <div class="dash-card-body">
                    <div class="input-group input-group-lg">
                        <input type="text" value="{{ $discount->code }}" id="discount-code" readonly
                               class="form-control font-monospace fw-bold text-center bg-warning-subtle text-warning border-warning"
                               style="letter-spacing:0.1em">
                        <button onclick="copyCode()" class="btn btn-warning fw-semibold" title="Copia">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                    <div class="form-text mt-2">Condividi questo codice con i clienti</div>
                </div>
            </div>

            {{-- Statistics --}}
            <div class="dash-card mb-3">
                <div class="dash-card-header">
                    <h3><i class="bi bi-bar-chart me-2 text-primary"></i>Statistiche</h3>
                </div>
                <div class="dash-card-body">
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <span class="small text-muted"><i class="bi bi-journal-bookmark me-2"></i>Prenotazioni</span>
                        <span class="fw-bold">{{ $stats['total_bookings'] }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <span class="small text-muted"><i class="bi bi-cash-coin me-2"></i>Sconto totale</span>
                        <span class="fw-bold text-success">€{{ number_format($stats['total_discount'], 2, ',', '.') }}</span>
                    </div>
                    @if($stats['usage_rate'] !== null)
                        <div class="d-flex align-items-center justify-content-between py-2">
                            <span class="small text-muted"><i class="bi bi-speedometer2 me-2"></i>Tasso utilizzo</span>
                            <span class="fw-bold">{{ $stats['usage_rate'] }}%</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info --}}
            <div class="dash-card mb-3">
                <div class="dash-card-header">
                    <h3><i class="bi bi-info-circle me-2 text-primary"></i>Informazioni</h3>
                </div>
                <div class="dash-card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom small">
                        <span class="text-muted"><i class="bi bi-calendar-plus me-2"></i>Creato</span>
                        <span class="text-dark">{{ $discount->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 small">
                        <span class="text-muted"><i class="bi bi-calendar-check me-2"></i>Aggiornato</span>
                        <span class="text-dark">{{ $discount->updated_at->format('d/m/Y H:i') }}</span>
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
                        L'eliminazione è permanente. Se ci sono prenotazioni associate, l'operazione potrebbe non essere consentita.
                    </p>
                    <button type="button" class="btn btn-outline-danger w-100 rounded-pill fw-semibold"
                            data-bs-toggle="modal" data-bs-target="#deleteDiscountModal">
                        <i class="bi bi-trash me-2"></i>Elimina codice
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete modal --}}
    <div class="modal fade" id="deleteDiscountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:1rem">
                <div class="modal-body text-center p-4">
                    <div class="mx-auto mb-3 rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center"
                         style="width:72px; height:72px">
                        <i class="bi bi-exclamation-triangle fs-2"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Eliminare il codice {{ $discount->code }}?</h4>
                    <p class="text-muted mb-0">L'operazione è irreversibile.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal">Annulla</button>
                    <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST" class="d-inline">
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

    {{-- Copy toast --}}
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="copyToast" class="toast align-items-center text-bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-check-circle me-2"></i>Codice copiato negli appunti
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function copyCode() {
        const input = document.getElementById('discount-code');
        navigator.clipboard.writeText(input.value).then(() => {
            const toastEl = document.getElementById('copyToast');
            new bootstrap.Toast(toastEl, { delay: 1800 }).show();
        });
    }
</script>
@endpush
