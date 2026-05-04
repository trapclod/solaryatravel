@extends('layouts.admin')

@section('title', 'Dettagli pagamento')

@php
    $statusMeta = [
        'pending'            => ['icon' => 'bi-hourglass-split',         'pill' => 's-pending',   'bg' => 'rgba(234,179,8,.08)',  'border' => 'rgba(234,179,8,.25)'],
        'processing'         => ['icon' => 'bi-arrow-repeat',            'pill' => 's-pending',   'bg' => 'rgba(59,130,246,.08)', 'border' => 'rgba(59,130,246,.25)'],
        'succeeded'          => ['icon' => 'bi-check-circle-fill',       'pill' => 's-confirmed', 'bg' => 'rgba(16,185,129,.08)', 'border' => 'rgba(16,185,129,.25)'],
        'failed'             => ['icon' => 'bi-x-octagon-fill',          'pill' => 's-cancelled', 'bg' => 'rgba(239,68,68,.08)',  'border' => 'rgba(239,68,68,.25)'],
        'cancelled'          => ['icon' => 'bi-slash-circle',             'pill' => 's-no_show',  'bg' => 'rgba(148,163,184,.1)', 'border' => 'rgba(148,163,184,.3)'],
        'refunded'           => ['icon' => 'bi-arrow-counterclockwise',   'pill' => 's-inactive', 'bg' => 'rgba(2,132,199,.08)',  'border' => 'rgba(2,132,199,.25)'],
        'partially_refunded' => ['icon' => 'bi-arrow-90deg-left',         'pill' => 's-pending',  'bg' => 'rgba(234,179,8,.08)',  'border' => 'rgba(234,179,8,.25)'],
    ];
    $sv = $payment->status->value;
    $meta = $statusMeta[$sv] ?? ['icon' => 'bi-circle', 'pill' => 's-pending', 'bg' => 'rgba(148,163,184,.1)', 'border' => 'rgba(148,163,184,.3)'];

    $gatewayMeta = [
        'stripe' => ['icon' => 'bi-stripe', 'label' => 'Stripe', 'class' => 'text-primary'],
        'paypal' => ['icon' => 'bi-paypal', 'label' => 'PayPal', 'class' => 'text-info'],
    ];
    $gw = $gatewayMeta[$payment->gateway] ?? ['icon' => 'bi-wallet2', 'label' => ucfirst($payment->gateway), 'class' => 'text-secondary'];
@endphp

@section('content')
    {{-- Page header --}}
    <div class="dash-page-header">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <a href="{{ route('admin.payments.index') }}" class="dash-icon-btn" title="Torna ai pagamenti">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-credit-card-2-front text-primary"></i>
                    Pagamento <span class="font-monospace text-muted">#{{ substr($payment->uuid, 0, 8) }}</span>
                </h1>
                <p class="mt-1 mb-0">
                    <i class="bi bi-clock-history me-1"></i>
                    {{ $payment->created_at->locale('it')->isoFormat('D MMM YYYY') }} alle {{ $payment->created_at->format('H:i') }}
                </p>
            </div>
        </div>
        <div>
            <span class="status-pill {{ $meta['pill'] }}" style="font-size:.95rem; padding:.5rem 1rem">
                <i class="bi {{ $meta['icon'] }}"></i>
                {{ $payment->status->label() }}
            </span>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 d-flex align-items-center gap-2 mb-3"
             style="background:rgba(16,185,129,.1); color:#059669">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-3 d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="row g-3">
        {{-- Main column --}}
        <div class="col-lg-8">
            {{-- Hero amount card --}}
            <div class="dash-card mb-3 overflow-hidden">
                <div class="dash-card-body p-4"
                     style="background: linear-gradient(135deg, {{ $meta['bg'] }}, rgba(254,252,232,.4)); border-bottom: 1px solid {{ $meta['border'] }}">
                    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3">
                        <div>
                            <div class="small text-muted text-uppercase fw-semibold mb-1">
                                <i class="bi bi-cash-coin me-1"></i>Importo
                            </div>
                            <div class="display-4 fw-bold text-dark lh-1">
                                €{{ number_format($payment->amount, 2, ',', '.') }}
                            </div>
                            <div class="small text-muted mt-1">
                                <i class="bi bi-globe me-1"></i>{{ strtoupper($payment->currency) }}
                            </div>
                        </div>
                        @if($payment->refunded_amount > 0)
                            <div class="text-end">
                                <div class="small text-muted text-uppercase fw-semibold mb-1">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Rimborsato
                                </div>
                                <div class="fs-2 fw-bold text-danger">
                                    -€{{ number_format($payment->refunded_amount, 2, ',', '.') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                @if($payment->fee_amount)
                    <div class="dash-card-body py-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="small text-muted">Commissioni</div>
                                <div class="fw-semibold text-dark">€{{ number_format($payment->fee_amount, 2, ',', '.') }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">Netto incassato</div>
                                <div class="fw-bold text-success">€{{ number_format($payment->net_amount, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Transaction details --}}
            <div class="dash-card mb-3">
                <div class="dash-card-header">
                    <h3><i class="bi bi-receipt-cutoff me-2 text-primary"></i>Dettagli transazione</h3>
                </div>
                <div class="dash-card-body">
                    <dl class="row g-0 mb-0">
                        <dt class="col-sm-4 py-2 small text-muted border-bottom">
                            <i class="bi bi-bank me-1"></i>Gateway
                        </dt>
                        <dd class="col-sm-8 py-2 border-bottom mb-0">
                            <i class="bi {{ $gw['icon'] }} fs-5 {{ $gw['class'] }} me-1"></i>
                            <span class="fw-semibold text-dark">{{ $gw['label'] }}</span>
                        </dd>

                        @if($payment->gateway_payment_id)
                            <dt class="col-sm-4 py-2 small text-muted border-bottom">
                                <i class="bi bi-hash me-1"></i>Payment ID
                            </dt>
                            <dd class="col-sm-8 py-2 border-bottom mb-0">
                                <code class="small text-dark">{{ $payment->gateway_payment_id }}</code>
                            </dd>
                        @endif

                        @if($payment->gateway_transaction_id)
                            <dt class="col-sm-4 py-2 small text-muted border-bottom">
                                <i class="bi bi-arrow-left-right me-1"></i>Transaction ID
                            </dt>
                            <dd class="col-sm-8 py-2 border-bottom mb-0">
                                <code class="small text-dark">{{ $payment->gateway_transaction_id }}</code>
                            </dd>
                        @endif

                        @if($payment->card_brand || $payment->card_last_four)
                            <dt class="col-sm-4 py-2 small text-muted border-bottom">
                                <i class="bi bi-credit-card me-1"></i>Carta
                            </dt>
                            <dd class="col-sm-8 py-2 border-bottom mb-0">
                                <span class="fw-semibold text-dark">{{ ucfirst($payment->card_brand ?? 'Carta') }}</span>
                                <span class="font-monospace text-muted ms-2">•••• {{ $payment->card_last_four }}</span>
                            </dd>
                        @endif

                        <dt class="col-sm-4 py-2 small text-muted {{ $payment->paid_at || $payment->refunded_at ? 'border-bottom' : '' }}">
                            <i class="bi bi-calendar-plus me-1"></i>Creato
                        </dt>
                        <dd class="col-sm-8 py-2 mb-0 {{ $payment->paid_at || $payment->refunded_at ? 'border-bottom' : '' }}">
                            <span class="text-dark">{{ $payment->created_at->format('d/m/Y H:i:s') }}</span>
                        </dd>

                        @if($payment->paid_at)
                            <dt class="col-sm-4 py-2 small text-muted {{ $payment->refunded_at ? 'border-bottom' : '' }}">
                                <i class="bi bi-check2-circle me-1 text-success"></i>Pagato
                            </dt>
                            <dd class="col-sm-8 py-2 mb-0 {{ $payment->refunded_at ? 'border-bottom' : '' }}">
                                <span class="text-success fw-semibold">{{ $payment->paid_at->format('d/m/Y H:i:s') }}</span>
                            </dd>
                        @endif

                        @if($payment->refunded_at)
                            <dt class="col-sm-4 py-2 small text-muted">
                                <i class="bi bi-arrow-counterclockwise me-1 text-danger"></i>Rimborsato
                            </dt>
                            <dd class="col-sm-8 py-2 mb-0">
                                <span class="text-danger fw-semibold">{{ $payment->refunded_at->format('d/m/Y H:i:s') }}</span>
                            </dd>
                        @endif
                    </dl>
                </div>
            </div>

            @if($payment->failure_reason)
                <div class="dash-card mb-3" style="border-color: rgba(239,68,68,.3)">
                    <div class="dash-card-body" style="background: rgba(239,68,68,.05)">
                        <h3 class="fs-6 fw-bold text-danger mb-2">
                            <i class="bi bi-exclamation-octagon-fill me-2"></i>Motivo del fallimento
                        </h3>
                        <p class="text-danger mb-0">{{ $payment->failure_reason }}</p>
                    </div>
                </div>
            @endif

            @if($payment->refund_reason)
                <div class="dash-card mb-3" style="border-color: rgba(2,132,199,.3)">
                    <div class="dash-card-body" style="background: rgba(2,132,199,.05)">
                        <h3 class="fs-6 fw-bold text-primary mb-2">
                            <i class="bi bi-info-circle-fill me-2"></i>Motivo del rimborso
                        </h3>
                        <p class="text-primary mb-0">{{ $payment->refund_reason }}</p>
                    </div>
                </div>
            @endif

            @if($payment->gateway_response)
                <div class="dash-card mb-3">
                    <div class="dash-card-header">
                        <h3><i class="bi bi-code-slash me-2 text-primary"></i>Risposta gateway</h3>
                    </div>
                    <div class="dash-card-body">
                        <pre class="bg-light rounded-3 p-3 small mb-0"
                             style="max-height:300px; overflow:auto"><code>{{ json_encode($payment->gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="position-sticky" style="top: 1rem">
                @if($payment->booking)
                    {{-- Linked booking --}}
                    <div class="dash-card mb-3">
                        <div class="dash-card-header">
                            <h3><i class="bi bi-receipt me-2 text-primary"></i>Prenotazione</h3>
                        </div>
                        <div class="dash-card-body">
                            <a href="{{ route('admin.bookings.show', $payment->booking) }}"
                               class="d-block text-primary fw-bold fs-5 text-decoration-none mb-3">
                                <i class="bi bi-link-45deg me-1"></i>{{ $payment->booking->booking_number }}
                            </a>
                            <div class="d-flex flex-column gap-2 small">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-water text-primary"></i>
                                    <span class="text-muted">Catamarano</span>
                                    <span class="ms-auto fw-semibold text-dark text-truncate">{{ $payment->booking->catamaran->name ?? '—' }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar-event text-primary"></i>
                                    <span class="text-muted">Data</span>
                                    <span class="ms-auto fw-semibold text-dark">{{ $payment->booking->booking_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-clock text-primary"></i>
                                    <span class="text-muted">Orario</span>
                                    <span class="ms-auto fw-semibold text-dark">{{ $payment->booking->timeSlot->name ?? '—' }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-people text-primary"></i>
                                    <span class="text-muted">Posti</span>
                                    <span class="ms-auto fw-semibold text-dark">{{ $payment->booking->seats }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Customer --}}
                    <div class="dash-card mb-3">
                        <div class="dash-card-header">
                            <h3><i class="bi bi-person me-2 text-primary"></i>Cliente</h3>
                        </div>
                        <div class="dash-card-body">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <span class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                      style="width:42px; height:42px; font-size:1rem">
                                    {{ strtoupper(substr($payment->booking->customer_first_name, 0, 1) . substr($payment->booking->customer_last_name, 0, 1)) }}
                                </span>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-dark text-truncate">
                                        {{ $payment->booking->customer_first_name }} {{ $payment->booking->customer_last_name }}
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-1 small">
                                <a href="mailto:{{ $payment->booking->customer_email }}" class="text-decoration-none text-secondary">
                                    <i class="bi bi-envelope me-1"></i>{{ $payment->booking->customer_email }}
                                </a>
                                @if($payment->booking->customer_phone)
                                    <a href="tel:{{ $payment->booking->customer_phone }}" class="text-decoration-none text-secondary">
                                        <i class="bi bi-telephone me-1"></i>{{ $payment->booking->customer_phone }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Refund action --}}
                @if(in_array($payment->status, [\App\Enums\PaymentStatus::SUCCEEDED, \App\Enums\PaymentStatus::PARTIALLY_REFUNDED]))
                    @php $maxRefund = $payment->amount - $payment->refunded_amount; @endphp
                    <div class="dash-card mb-3" style="border-color: rgba(239,68,68,.3)">
                        <div class="dash-card-header" style="background: rgba(239,68,68,.05)">
                            <h3><i class="bi bi-arrow-counterclockwise me-2 text-danger"></i>Rimborso</h3>
                        </div>
                        <div class="dash-card-body">
                            @if($maxRefund > 0)
                                <p class="small text-muted mb-3">
                                    Importo massimo rimborsabile:
                                    <strong class="text-dark">€{{ number_format($maxRefund, 2, ',', '.') }}</strong>
                                </p>
                                <form action="{{ route('admin.payments.refund', $payment) }}" method="POST"
                                      onsubmit="return confirm('Sei sicuro di voler processare questo rimborso?');">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary mb-1">
                                            <i class="bi bi-cash-coin me-1"></i>Importo
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">€</span>
                                            <input type="number" name="amount" step="0.01" min="0.01" max="{{ $maxRefund }}"
                                                   value="{{ old('amount', $maxRefund) }}"
                                                   class="form-control @error('amount') is-invalid @enderror" required>
                                        </div>
                                        @error('amount')
                                            <div class="small text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold text-secondary mb-1">
                                            <i class="bi bi-chat-left-text me-1"></i>Motivo
                                        </label>
                                        <textarea name="reason" rows="3"
                                                  class="form-control @error('reason') is-invalid @enderror"
                                                  placeholder="Inserisci il motivo del rimborso..."
                                                  required>{{ old('reason') }}</textarea>
                                        @error('reason')
                                            <div class="small text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100 rounded-pill fw-semibold">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Processa rimborso
                                    </button>
                                </form>
                            @else
                                <div class="text-center py-3 text-muted">
                                    <i class="bi bi-check-circle fs-1 d-block mb-2 text-success opacity-75"></i>
                                    <p class="small mb-0">Questo pagamento è già stato completamente rimborsato.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
