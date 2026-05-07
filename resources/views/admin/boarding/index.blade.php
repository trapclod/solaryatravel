@extends('layouts.admin')

@section('title', 'Imbarco passeggeri')

@section('content')
    <div class="dash-page-header">
        <div>
            <h1>Imbarco passeggeri</h1>
            <p>Seleziona una partenza per avviare lo scanner QR e registrare gli imbarchi.</p>
        </div>
        <form method="GET" class="d-flex gap-2">
            <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="form-control" onchange="this.form.submit()">
            <a href="{{ route('admin.boarding.index') }}" class="btn btn-outline-secondary">Oggi</a>
        </form>
    </div>

    @if($departures->isEmpty())
        <div class="dash-card">
            <div class="dash-card-body text-center py-5">
                <i class="bi bi-calendar-x display-3 text-muted opacity-50 d-block mb-3"></i>
                <h3 class="fw-bold mb-2">Nessuna partenza programmata</h3>
                <p class="text-muted mb-0">Per il giorno {{ $date->format('d/m/Y') }} non ci sono partenze in calendario.</p>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($departures as $dep)
                <div class="col-md-6 col-xl-4">
                    <a href="{{ route('admin.boarding.show', $dep) }}" class="text-decoration-none">
                        <div class="dash-card h-100">
                            <div class="dash-card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h3 class="h6 mb-0 fw-bold text-dark">{{ $dep->tour?->name ?? 'Tour non disponibile' }}</h3>
                                    <span class="badge text-bg-primary">{{ $dep->confirmed_bookings_count }} prenot.</span>
                                </div>
                                <div class="text-muted small mb-3">
                                    <i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($dep->departure_date)->format('d/m/Y') }}
                                    <i class="bi bi-clock ms-2 me-1"></i>{{ \Carbon\Carbon::parse($dep->start_time)->format('H:i') }}
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="small text-muted">Stato: <strong class="text-dark">{{ $dep->status }}</strong></span>
                                    <span class="text-primary fw-semibold small">Avvia <i class="bi bi-arrow-right"></i></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
@endsection
