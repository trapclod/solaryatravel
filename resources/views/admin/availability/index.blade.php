@extends('layouts.admin')

@section('title', 'Gestione Disponibilità')

@section('content')
    {{-- Page header --}}
    <div class="dash-page-header">
        <div>
            <h1>Gestione disponibilità</h1>
            <p>Seleziona un catamarano per gestire la sua disponibilità, bloccare date o impostare posti.</p>
        </div>
    </div>

    {{-- Time slots overview --}}
    <div class="dash-card mb-4">
        <div class="dash-card-header">
            <h3><i class="bi bi-clock-history me-2 text-primary"></i>Fasce orarie attive</h3>
            <span class="small text-muted">{{ count($timeSlots) }} configurate</span>
        </div>
        <div class="dash-card-body">
            <div class="row g-2">
                @forelse($timeSlots as $slot)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="time-slot-pill">
                            <div class="time-slot-icon">
                                <i class="bi bi-sun"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate">{{ $slot->name }}</div>
                                <div class="small text-muted">
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                                    – {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-clock fs-1 d-block mb-2 opacity-50"></i>
                            Nessuna fascia oraria configurata
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Catamarans grid --}}
    <h2 class="h5 fw-bold text-dark mt-4 mb-3">
        <i class="bi bi-grid-3x3-gap me-2 text-primary"></i>Seleziona un catamarano
    </h2>

    <div class="row g-3">
        @forelse($catamarans as $catamaran)
            <div class="col-md-6 col-xl-4">
                <a href="{{ route('admin.availability.calendar', $catamaran) }}"
                   class="cat-card text-decoration-none d-flex flex-column h-100 cat-card-link">
                    <div class="cat-card-media">
                        @if($catamaran->images->first())
                            <img src="{{ Storage::url($catamaran->images->first()->path) }}"
                                 alt="{{ $catamaran->name }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 bg-light">
                                <i class="bi bi-water fs-1 text-muted opacity-50"></i>
                            </div>
                        @endif

                        @if($catamaran->is_active)
                            <span class="cat-status-badge active">
                                <i class="bi bi-check-circle-fill"></i>Attivo
                            </span>
                        @else
                            <span class="cat-status-badge inactive">
                                <i class="bi bi-pause-circle-fill"></i>Inattivo
                            </span>
                        @endif

                        <div class="cat-card-cta">
                            <span><i class="bi bi-calendar3 me-2"></i>Gestisci disponibilità</span>
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </div>

                    <div class="cat-card-body">
                        <h3 class="h6 fw-bold mb-2 text-dark">{{ $catamaran->name }}</h3>
                        <div class="d-flex align-items-center gap-3 small text-muted">
                            <span><i class="bi bi-people me-1"></i>{{ $catamaran->capacity }} posti</span>
                            <span><i class="bi bi-journal-bookmark me-1"></i>{{ $catamaran->bookings_count }} prenotazioni</span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="dash-card">
                    <div class="dash-card-body text-center py-5">
                        <div class="mx-auto mb-3 rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center"
                             style="width:72px; height:72px">
                            <i class="bi bi-water fs-2"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-2">Nessun catamarano</h3>
                        <p class="text-muted mb-3">Aggiungi prima dei catamarani per gestire la disponibilità.</p>
                        <a href="{{ route('admin.catamarans.create') }}" class="btn btn-primary rounded-pill px-4 fw-semibold">
                            <i class="bi bi-plus-lg me-2"></i>Nuovo catamarano
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
@endsection
