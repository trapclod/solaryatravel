@extends('layouts.admin')

@section('title', 'Codici sconto')

@php
    function discountStatus($d) {
        if (!$d->is_active) return ['inactive', 'Inattivo', 'bi-pause-circle-fill'];
        if ($d->valid_until && $d->valid_until->isPast()) return ['cancelled', 'Scaduto', 'bi-x-circle-fill'];
        if ($d->usage_limit && $d->usage_count >= $d->usage_limit) return ['no_show', 'Esaurito', 'bi-exclamation-circle-fill'];
        if ($d->valid_from && $d->valid_from->isFuture()) return ['pending', 'Futuro', 'bi-hourglass-split'];
        return ['confirmed', 'Attivo', 'bi-check-circle-fill'];
    }
@endphp

@section('content')
    {{-- Page header --}}
    <div class="dash-page-header">
        <div>
            <h1>Codici sconto</h1>
            <p>Gestisci i codici promozionali, le loro condizioni di utilizzo e validità.</p>
        </div>
        <div>
            <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary rounded-pill px-3 fw-semibold">
                <i class="bi bi-plus-lg me-2"></i>Nuovo codice
            </a>
        </div>
    </div>

    {{-- Mini stats --}}
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat is-active">
                <div class="dash-mini-stat-label"><i class="bi bi-ticket-perforated me-1"></i>Totali</div>
                <div class="dash-mini-stat-value">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-check-circle me-1"></i>Attivi</div>
                <div class="dash-mini-stat-value text-success">{{ $stats['active'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-x-circle me-1"></i>Scaduti</div>
                <div class="dash-mini-stat-value text-danger">{{ $stats['expired'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-graph-up me-1"></i>Utilizzi totali</div>
                <div class="dash-mini-stat-value text-primary">{{ $stats['total_usage'] }}</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="dash-filter-bar mb-3">
        <form action="{{ route('admin.discounts.index') }}" method="GET" class="row g-2 w-100 align-items-center">
            <div class="col-12 col-md">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cerca codice o descrizione..."
                           class="form-control border-start-0">
                </div>
            </div>
            <div class="col-6 col-md-auto">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Tutti gli stati</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>✅ Attivi</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>⏸ Inattivi</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>❌ Scaduti</option>
                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>⏳ Futuri</option>
                </select>
            </div>
            <div class="col-6 col-md-auto">
                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="">Tutti i tipi</option>
                    <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>% Percentuale</option>
                    <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>€ Fisso</option>
                </select>
            </div>
            <div class="col-12 col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-pill px-3 fw-semibold">
                    <i class="bi bi-funnel me-1"></i>Filtra
                </button>
                @if(request()->hasAny(['search', 'status', 'type']))
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-light border rounded-pill px-3">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="dash-card mb-4">
        <div class="table-responsive">
            <table class="dash-table mb-0">
                <thead>
                    <tr>
                        <th>Codice</th>
                        <th>Sconto</th>
                        <th>Validità</th>
                        <th>Utilizzi</th>
                        <th>Stato</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($discounts as $discount)
                        @php [$st, $stLabel, $stIcon] = discountStatus($discount); @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="rounded-3 bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                          style="width:40px; height:40px">
                                        <i class="bi bi-ticket-perforated fs-5"></i>
                                    </span>
                                    <div class="min-w-0">
                                        <div class="font-monospace fw-bold text-dark">{{ $discount->code }}</div>
                                        @if($discount->description)
                                            <div class="small text-muted text-truncate" style="max-width:240px">
                                                {{ Str::limit($discount->description, 50) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($discount->discount_type === 'percentage')
                                    <span class="fw-bold text-primary fs-5">{{ rtrim(rtrim(number_format($discount->discount_value, 2, ',', ''), '0'), ',') }}%</span>
                                @else
                                    <span class="fw-bold text-primary fs-5">€{{ number_format($discount->discount_value, 2, ',', '.') }}</span>
                                @endif
                                @if($discount->min_amount)
                                    <div class="small text-muted">Min. €{{ number_format($discount->min_amount, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="small">
                                @if($discount->valid_from || $discount->valid_until)
                                    @if($discount->valid_from)
                                        <div class="text-muted">
                                            <i class="bi bi-arrow-right-short"></i>
                                            {{ $discount->valid_from->format('d/m/Y') }}
                                        </div>
                                    @endif
                                    @if($discount->valid_until)
                                        <div class="{{ $discount->valid_until->isPast() ? 'text-danger fw-semibold' : 'text-muted' }}">
                                            <i class="bi bi-arrow-left-short"></i>
                                            {{ $discount->valid_until->format('d/m/Y') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-muted"><i class="bi bi-infinity me-1"></i>Sempre valido</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-baseline gap-1">
                                    <span class="fw-bold text-dark">{{ $discount->usage_count }}</span>
                                    <span class="small text-muted">/ {{ $discount->usage_limit ?? '∞' }}</span>
                                </div>
                                @if($discount->usage_limit)
                                    @php $pct = min(100, ($discount->usage_count / $discount->usage_limit) * 100); @endphp
                                    <div class="progress mt-1" style="height:5px; max-width:120px">
                                        <div class="progress-bar {{ $pct >= 100 ? 'bg-danger' : ($pct >= 80 ? 'bg-warning' : 'bg-primary') }}"
                                             style="width: {{ $pct }}%"></div>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="status-pill s-{{ $st }}"><i class="bi {{ $stIcon }}"></i>{{ $stLabel }}</span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-1">
                                    <button type="button" class="dash-icon-btn copy-code-btn"
                                            data-code="{{ $discount->code }}" title="Copia codice">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                    <form action="{{ route('admin.discounts.toggle', $discount) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dash-icon-btn {{ $discount->is_active ? '' : 'is-success' }}"
                                                title="{{ $discount->is_active ? 'Disattiva' : 'Attiva' }}">
                                            <i class="bi {{ $discount->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.discounts.show', $discount) }}" class="dash-icon-btn" title="Dettagli">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.discounts.edit', $discount) }}" class="dash-icon-btn is-primary" title="Modifica">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Eliminare il codice {{ $discount->code }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dash-icon-btn is-danger" title="Elimina">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="text-center py-5">
                                    <div class="mx-auto mb-3 rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center"
                                         style="width:72px; height:72px">
                                        <i class="bi bi-ticket-perforated fs-2"></i>
                                    </div>
                                    <h3 class="h5 fw-bold mb-2">Nessun codice trovato</h3>
                                    <p class="text-muted mb-3">Crea il tuo primo codice promozionale per iniziare.</p>
                                    <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                        <i class="bi bi-plus-lg me-2"></i>Crea codice
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($discounts->hasPages())
            <div class="dash-card-body">
                {{ $discounts->links() }}
            </div>
        @endif
    </div>

    {{-- Toast for copy --}}
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
    document.addEventListener('DOMContentLoaded', function () {
        const toastEl = document.getElementById('copyToast');
        const toast = new bootstrap.Toast(toastEl, { delay: 1800 });

        document.querySelectorAll('.copy-code-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const code = btn.dataset.code;
                navigator.clipboard.writeText(code).then(() => toast.show());
            });
        });
    });
</script>
@endpush
