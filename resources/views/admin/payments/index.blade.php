@extends('layouts.admin')

@section('title', 'Pagamenti')

@php
    $statusMeta = [
        'pending'            => ['icon' => 'bi-hourglass-split',   'pill' => 's-pending',   'text' => 'text-warning'],
        'processing'         => ['icon' => 'bi-arrow-repeat',      'pill' => 's-pending',   'text' => 'text-info'],
        'succeeded'          => ['icon' => 'bi-check-circle-fill', 'pill' => 's-confirmed', 'text' => 'text-success'],
        'failed'             => ['icon' => 'bi-x-octagon-fill',    'pill' => 's-cancelled', 'text' => 'text-danger'],
        'cancelled'          => ['icon' => 'bi-slash-circle',      'pill' => 's-no_show',   'text' => 'text-secondary'],
        'refunded'           => ['icon' => 'bi-arrow-counterclockwise', 'pill' => 's-inactive', 'text' => 'text-primary'],
        'partially_refunded' => ['icon' => 'bi-arrow-90deg-left',  'pill' => 's-pending',   'text' => 'text-warning'],
    ];
    $gatewayMeta = [
        'stripe' => ['icon' => 'bi-stripe',     'label' => 'Stripe',  'class' => 'text-primary'],
        'paypal' => ['icon' => 'bi-paypal',     'label' => 'PayPal',  'class' => 'text-info'],
    ];
@endphp

@section('content')
    {{-- Page header --}}
    <div class="dash-page-header">
        <div>
            <h1>Pagamenti</h1>
            <p><i class="bi bi-credit-card-2-front me-1"></i>Gestione transazioni e rimborsi</p>
        </div>
    </div>

    {{-- Mini stats --}}
    <div class="row g-2 mb-3">
        <div class="col-6 col-md">
            <div class="dash-mini-stat is-active">
                <div class="dash-mini-stat-label"><i class="bi bi-receipt me-1"></i>Totale</div>
                <div class="dash-mini-stat-value">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-check-circle me-1"></i>Completati</div>
                <div class="dash-mini-stat-value text-success">{{ $stats['succeeded'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-hourglass-split me-1"></i>In attesa</div>
                <div class="dash-mini-stat-value text-warning">{{ $stats['pending'] }}</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-cash-coin me-1"></i>Incassato</div>
                <div class="dash-mini-stat-value text-primary">€{{ number_format($stats['total_amount'], 2, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-arrow-counterclockwise me-1"></i>Rimborsato</div>
                <div class="dash-mini-stat-value text-danger">€{{ number_format($stats['refunded_amount'], 2, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="dash-card mb-3">
        <div class="dash-card-body">
            <form action="{{ route('admin.payments.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">Cerca</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="ID pagamento, prenotazione..." class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Stato</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Tutti</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Gateway</label>
                    <select name="gateway" class="form-select" onchange="this.form.submit()">
                        <option value="">Tutti</option>
                        <option value="stripe" {{ request('gateway') === 'stripe' ? 'selected' : '' }}>Stripe</option>
                        <option value="paypal" {{ request('gateway') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">Da</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">A</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary rounded-pill px-3 fw-semibold">
                        <i class="bi bi-funnel me-1"></i>Filtra
                    </button>
                    @if(request()->hasAny(['search', 'status', 'gateway', 'from', 'to']))
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-light border rounded-pill px-3 fw-semibold">
                            <i class="bi bi-x-lg me-1"></i>Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Payments table --}}
    <div class="dash-card mb-4">
        <div class="table-responsive">
            <table class="dash-table mb-0">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Prenotazione</th>
                        <th>Cliente</th>
                        <th>Gateway</th>
                        <th class="text-end">Importo</th>
                        <th class="text-center">Stato</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        @php
                            $sv = $payment->status->value;
                            $meta = $statusMeta[$sv] ?? ['icon' => 'bi-circle', 'pill' => 's-pending', 'text' => 'text-secondary'];
                            $gw = $gatewayMeta[$payment->gateway] ?? ['icon' => 'bi-wallet2', 'label' => ucfirst($payment->gateway), 'class' => 'text-secondary'];
                        @endphp
                        <tr>
                            <td>
                                <div class="small fw-semibold text-dark">
                                    <i class="bi bi-calendar-event me-1 text-muted"></i>
                                    {{ $payment->created_at->format('d/m/Y') }}
                                </div>
                                <div class="small text-muted">{{ $payment->created_at->format('H:i') }}</div>
                            </td>
                            <td>
                                @if($payment->booking)
                                    <a href="{{ route('admin.bookings.show', $payment->booking) }}"
                                       class="text-primary fw-semibold text-decoration-none">
                                        <i class="bi bi-receipt me-1"></i>{{ $payment->booking->booking_number }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($payment->booking)
                                    <div class="fw-semibold text-dark text-truncate" style="max-width:200px">
                                        {{ $payment->booking->customer_first_name }} {{ $payment->booking->customer_last_name }}
                                    </div>
                                    <div class="small text-muted text-truncate" style="max-width:200px">
                                        {{ $payment->booking->customer_email }}
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi {{ $gw['icon'] }} fs-5 {{ $gw['class'] }}"></i>
                                    <div>
                                        <div class="small fw-semibold text-dark">{{ $gw['label'] }}</div>
                                        @if($payment->card_last_four)
                                            <div class="small text-muted font-monospace">•••• {{ $payment->card_last_four }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="fw-bold text-dark">€{{ number_format($payment->amount, 2, ',', '.') }}</div>
                                @if($payment->refunded_amount > 0)
                                    <div class="small text-danger">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                                        -€{{ number_format($payment->refunded_amount, 2, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="status-pill {{ $meta['pill'] }}">
                                    <i class="bi {{ $meta['icon'] }}"></i>
                                    {{ $payment->status->label() }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.payments.show', $payment) }}"
                                   class="dash-icon-btn" title="Dettagli">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-credit-card-2-front fs-1 d-block mb-2 opacity-50"></i>
                                    <p class="fw-semibold mb-1">Nessun pagamento trovato</p>
                                    <p class="small mb-0">Prova a modificare i filtri di ricerca</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="border-top px-3 py-3">
                {{ $payments->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
