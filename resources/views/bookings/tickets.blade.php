@extends('layouts.app')

@section('title', 'Biglietti · ' . $booking->booking_number)

@push('styles')
<style>
    @media print {
        @page { size: A4; margin: 12mm; }
        .no-print { display: none !important; }
        body { background: #fff !important; }
        .ticket { page-break-inside: avoid; box-shadow: none !important; }
    }
    .ticket {
        background: linear-gradient(135deg, #fff 0%, #f7faff 100%);
        border: 2px dashed #0066cc;
        border-radius: 1.25rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .ticket::before {
        content: '';
        position: absolute; top: 0; left: 0; bottom: 0;
        width: 8px;
        background: repeating-linear-gradient(0deg, #0066cc 0 6px, transparent 6px 12px);
    }
    .ticket-stub {
        border-left: 2px dashed #cbd5e1;
        padding-left: 1.5rem;
    }
    .ticket-qr { background: #fff; padding: 8px; border-radius: .5rem; }
    .ticket-label { font-size: .7rem; text-transform: uppercase; letter-spacing: .08em; color: #64748b; font-weight: 600; }
    .ticket-value { font-size: 1.1rem; font-weight: 700; color: #0f172a; }
    .ticket-pax-name { font-size: 1.4rem; font-weight: 800; color: #0066cc; }
    .ticket-code { font-family: ui-monospace, monospace; font-size: .85rem; color: #475569; }
</style>
@endpush

@section('content')
<div class="container py-4" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h1 class="h4 fw-bold mb-1">I tuoi biglietti</h1>
            <p class="text-muted mb-0">Prenotazione <span class="font-monospace">#{{ $booking->booking_number }}</span></p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('booking.show', $booking->uuid) }}" class="btn btn-outline-secondary rounded-pill">
                <i class="bi bi-arrow-left me-1"></i>Prenotazione
            </a>
            <button onclick="window.print()" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-printer me-1"></i>Stampa biglietti
            </button>
        </div>
    </div>

    <div class="alert alert-info no-print d-flex align-items-start">
        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
        <div>
            <strong>Ogni passeggero ha il proprio biglietto.</strong> Mostrare il QR code al momento dell'imbarco
            (anche dal cellulare). Il personale lo scansionerà per verificare la presenza del passeggero.
        </div>
    </div>

    @forelse($booking->seatRecords as $seat)
        <div class="ticket">
            <div class="row g-3 align-items-center">
                <div class="col-md-7">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="ticket-label">Passeggero</div>
                            <div class="ticket-pax-name">
                                @if($seat->is_primary)<i class="bi bi-star-fill text-warning"></i> @endif
                                {{ $seat->guest_full_name ?: $booking->customer_full_name }}
                            </div>
                            @if($seat->ageBracket)
                                <span class="badge text-bg-light border mt-1">{{ $seat->ageBracket->label }}</span>
                            @endif
                        </div>
                        <div class="text-end">
                            <div class="ticket-label">Posto</div>
                            <div class="ticket-value">#{{ $seat->seat_number ?? $loop->iteration }}</div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="ticket-label">Tour</div>
                            <div class="ticket-value">{{ $booking->tour->name ?? '—' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="ticket-label">Data</div>
                            <div class="ticket-value">
                                @if($booking->departure)
                                    {{ \Carbon\Carbon::parse($booking->departure->departure_date)->format('d/m/Y') }}
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="ticket-label">Partenza</div>
                            <div class="ticket-value">
                                @if($booking->departure)
                                    <i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($booking->departure->start_time)->format('H:i') }}
                                @endif
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="ticket-label">Catamarano</div>
                            <div class="ticket-value">{{ $seat->catamaran->name ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-5 ticket-stub text-center">
                    <img src="{{ route('booking.seat.qr', $seat->qr_code) }}"
                         alt="QR biglietto"
                         class="ticket-qr img-fluid"
                         style="max-width: 200px;">
                    <div class="ticket-code mt-2">{{ $seat->qr_code }}</div>
                    <div class="small text-muted mt-1">Prenotazione #{{ $booking->booking_number }}</div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-warning">Nessun passeggero registrato per questa prenotazione.</div>
    @endforelse
</div>
@endsection
