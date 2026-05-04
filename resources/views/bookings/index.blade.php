@extends('layouts.app')

@section('title', 'Le Mie Prenotazioni - Solarya Travel')

@php
    $statusColors = [
        'pending' => 'warning',
        'confirmed' => 'success',
        'cancelled' => 'danger',
        'completed' => 'primary',
        'no_show' => 'secondary',
    ];
    $statusLabels = [
        'pending' => 'In attesa',
        'confirmed' => 'Confermata',
        'cancelled' => 'Annullata',
        'completed' => 'Completata',
        'no_show' => 'No show',
    ];
@endphp

@section('content')
    <section class="py-5 bg-sand-50 min-vh-75">
        <div class="container py-4">
            <div class="mx-auto" style="max-width:960px">
                <h1 class="h2 fw-bold text-navy mb-4 font-serif">Le Mie Prenotazioni</h1>

                @if($bookings->isEmpty())
                    <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                        <i class="bi bi-calendar-x display-1 text-muted opacity-50 mb-3"></i>
                        <h2 class="h4 fw-semibold text-secondary mb-2">Nessuna prenotazione</h2>
                        <p class="text-muted mb-4">Non hai ancora effettuato prenotazioni.</p>
                        <div>
                            <a href="{{ route('booking.start') }}" class="btn btn-primary btn-lg rounded-pill px-4 fw-semibold">
                                Prenota Ora <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="vstack gap-3">
                        @foreach($bookings as $booking)
                            @php $color = $statusColors[$booking->status] ?? 'secondary'; @endphp
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-body p-4">
                                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <span class="font-monospace small text-muted">#{{ $booking->booking_number }}</span>
                                                <span class="badge rounded-pill bg-{{ $color }}-subtle text-{{ $color }} px-3 py-2">
                                                    {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                                                </span>
                                            </div>
                                            <h3 class="h5 fw-semibold text-navy mb-2">{{ $booking->catamaran->name ?? 'Catamarano' }}</h3>
                                            <div class="d-flex flex-wrap gap-3 small text-secondary">
                                                <span><i class="bi bi-calendar-event text-muted me-1"></i>{{ $booking->booking_date->format('d/m/Y') }}</span>
                                                @if($booking->timeSlot)
                                                    <span><i class="bi bi-clock text-muted me-1"></i>{{ $booking->timeSlot->start_time }} - {{ $booking->timeSlot->end_time }}</span>
                                                @endif
                                                <span><i class="bi bi-people text-muted me-1"></i>{{ $booking->adults_count }} adulti@if($booking->children_count > 0), {{ $booking->children_count }} bambini @endif</span>
                                            </div>
                                        </div>
                                        <div class="text-md-end">
                                            <p class="h3 fw-bold text-primary mb-2">€{{ number_format($booking->total_amount, 2, ',', '.') }}</p>
                                            <a href="{{ route('booking.show', $booking->uuid) }}" class="btn btn-navy btn-sm rounded-pill px-3 fw-semibold">
                                                Dettagli <i class="bi bi-chevron-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $bookings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
