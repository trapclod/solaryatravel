@extends('layouts.app')

@section('title', 'Escursioni in Catamarano di Lusso')
@section('meta_description', 'Scopri le esperienze esclusive in catamarano con Solarya Travel. Escursioni mezza giornata, giornata intera e charter privati lungo la costa.')

@section('content')
    {{-- Hero Section --}}
    <section class="position-relative d-flex align-items-center justify-content-center text-center text-white overflow-hidden" style="min-height: 90vh;">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('/images/hero-catamaran.jpg'); background-size: cover; background-position: center;"></div>
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(15,23,42,0.85), rgba(15,23,42,0.45));"></div>

        <div class="position-relative container py-5" style="z-index: 2;">
            <h1 class="font-serif fw-bold display-3 mb-3">Vivi il Mare come Mai Prima</h1>
            <p class="lead mb-5 mx-auto" style="max-width: 720px;">
                Escursioni esclusive in catamarano lungo le coste più belle.
                Comfort, eleganza e servizio impeccabile per un'esperienza indimenticabile.
            </p>

            {{-- Search bar --}}
            <form method="GET" action="{{ route('catamarans.index') }}" class="mx-auto mb-5 text-start" style="max-width: 960px;">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom d-flex align-items-center gap-2 py-3 px-4">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle text-warning" style="width:28px;height:28px;background:#0f172a;">
                            <i class="bi bi-geo-alt-fill small"></i>
                        </span>
                        <span class="text-uppercase fw-bold small text-navy" style="letter-spacing:.2em;">Trova la tua escursione</span>
                    </div>
                    <div class="p-3 p-md-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-12 col-md">
                                <label for="hero_date" class="form-label small text-uppercase fw-bold text-muted" style="letter-spacing:.15em;">
                                    <i class="bi bi-calendar3 me-1"></i> Data
                                </label>
                                <input id="hero_date" type="date" name="date" min="{{ $minBookingDate }}" required class="form-control form-control-lg">
                            </div>
                            <div class="col-6 col-md-2">
                                <label for="hero_adults" class="form-label small text-uppercase fw-bold text-muted" style="letter-spacing:.15em;">
                                    <i class="bi bi-person me-1"></i> Adulti
                                </label>
                                <input id="hero_adults" type="number" name="adults" min="1" max="20" value="2" required class="form-control form-control-lg">
                            </div>
                            <div class="col-6 col-md-2">
                                <label for="hero_children" class="form-label small text-uppercase fw-bold text-muted" style="letter-spacing:.15em;">
                                    <i class="bi bi-people me-1"></i> Bambini
                                </label>
                                <input id="hero_children" type="number" name="children" min="0" max="20" value="0" required class="form-control form-control-lg">
                            </div>
                            <div class="col-12 col-md-auto">
                                <button type="submit" class="btn btn-gold btn-lg w-100 rounded-pill px-4">
                                    <i class="bi bi-search me-2"></i> Cerca
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-2 bg-light border-top small text-muted">
                        <i class="bi bi-check-circle-fill text-primary me-1"></i>
                        Tutti i catamarani disponibili — <strong class="text-navy">cancellazione gratuita fino a 24h prima</strong>
                    </div>
                </div>
            </form>

            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                <a href="{{ route('booking.start') }}" class="btn btn-gold btn-lg rounded-pill px-4">
                    Prenota Ora <i class="bi bi-arrow-right ms-2"></i>
                </a>
                <a href="{{ route('catamarans.index') }}" class="btn btn-outline-light btn-lg rounded-pill px-4">
                    Scopri i Catamarani
                </a>
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section class="section bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="font-serif fw-bold text-navy mb-3">Un'Esperienza Senza Pari</h2>
                <p class="lead text-muted mx-auto" style="max-width: 600px;">Ogni dettaglio è curato per offrirti momenti di puro relax e meraviglia</p>
            </div>
            <div class="row g-4">
                @php
                    $features = [
                        ['bi-stars', 'Lusso Accessibile', 'Catamarani di ultima generazione con ogni comfort a bordo'],
                        ['bi-people-fill', 'Equipaggio Esperto', 'Skipper professionisti e staff dedicato al tuo benessere'],
                        ['bi-clock-history', 'Flessibilità Totale', 'Mezza giornata, giornata intera o escursione privata'],
                        ['bi-credit-card-2-back', 'Prenotazione Facile', 'Sistema di booking online sicuro e conferma immediata'],
                    ];
                @endphp
                @foreach($features as [$icon, $title, $text])
                    <div class="col-12 col-md-6 col-lg-3 text-center">
                        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
                            <i class="bi {{ $icon }} fs-2"></i>
                        </div>
                        <h3 class="h5 fw-semibold text-navy mb-2">{{ $title }}</h3>
                        <p class="text-muted">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Catamarans --}}
    <section class="section bg-sand-50">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="font-serif fw-bold text-navy mb-3">La Nostra Flotta</h2>
                <p class="lead text-muted mx-auto" style="max-width: 600px;">Scegli il catamarano perfetto per la tua avventura</p>
            </div>
            <div class="row g-4">
                @foreach($catamarans as $catamaran)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 card-hover overflow-hidden">
                            <div class="ratio ratio-4x3 bg-light">
                                @if($catamaran->primaryImage)
                                    <img src="{{ $catamaran->primaryImage->url }}" alt="{{ $catamaran->name }}" class="object-fit-cover">
                                @else
                                    <div class="d-flex align-items-center justify-content-center text-muted">
                                        <i class="bi bi-image fs-1"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body">
                                <h3 class="h5 fw-semibold text-navy mb-2">{{ $catamaran->name }}</h3>
                                <p class="small text-muted mb-3">{{ $catamaran->description_short }}</p>
                                <div class="d-flex justify-content-between small text-muted mb-3">
                                    <span><i class="bi bi-people me-1"></i> {{ $catamaran->capacity }} ospiti</span>
                                    @if($catamaran->length_meters)
                                        <span>{{ $catamaran->length_meters }}m</span>
                                    @endif
                                </div>
                                <hr class="my-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <small class="text-muted">da</small>
                                        <span class="h4 fw-bold text-primary mb-0">€{{ number_format($catamaran->price_per_person_half_day, 0) }}</span>
                                        <small class="text-muted">/persona</small>
                                    </div>
                                    <a href="{{ route('catamarans.show', $catamaran->slug) }}" class="btn btn-link text-primary fw-medium text-decoration-none p-0">
                                        Scopri <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-5">
                <a href="{{ route('catamarans.index') }}" class="btn btn-navy btn-lg rounded-pill px-4">Vedi Tutti i Catamarani</a>
            </div>
        </div>
    </section>

    {{-- Stats --}}
    <section class="section bg-gradient-navy text-white">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-6 col-md-3">
                    <div class="display-4 fw-bold text-warning mb-2">{{ number_format($stats['happy_guests']) }}+</div>
                    <div class="text-white-50">Ospiti Felici</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="display-4 fw-bold text-warning mb-2">{{ $stats['years_experience'] }}</div>
                    <div class="text-white-50">Anni di Esperienza</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="display-4 fw-bold text-warning mb-2">{{ number_format($stats['excursions']) }}+</div>
                    <div class="text-white-50">Escursioni</div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="display-4 fw-bold text-warning mb-2">{{ $stats['catamarans'] }}</div>
                    <div class="text-white-50">Catamarani</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <section class="section bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="font-serif fw-bold text-navy mb-3">Cosa Dicono i Nostri Ospiti</h2>
                <p class="lead text-muted mx-auto" style="max-width: 600px;">Storie ed esperienze di chi ha vissuto l'avventura Solarya</p>
            </div>
            <div class="row g-4">
                @foreach($testimonials as $testimonial)
                    <div class="col-12 col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                            <div class="mb-3 text-warning">
                                @for($i = 0; $i < $testimonial['rating']; $i++)<i class="bi bi-star-fill"></i>@endfor
                            </div>
                            <p class="text-muted fst-italic mb-3">"{{ $testimonial['text'] }}"</p>
                            <div class="d-flex align-items-center mt-auto">
                                <div class="bg-primary-subtle text-primary fw-semibold rounded-circle d-flex align-items-center justify-content-center me-3" style="width:40px;height:40px;">
                                    {{ substr($testimonial['name'], 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-semibold text-navy">{{ $testimonial['name'] }}</div>
                                    <small class="text-muted">{{ $testimonial['location'] }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="section bg-gradient-primary text-white">
        <div class="container text-center">
            <h2 class="font-serif fw-bold mb-3">Pronto per l'Avventura?</h2>
            <p class="lead mb-4 mx-auto" style="max-width: 600px;">Prenota oggi la tua escursione in catamarano e vivi un'esperienza indimenticabile</p>
            <a href="{{ route('booking.start') }}" class="btn btn-light btn-lg rounded-pill px-4 fw-semibold text-primary">
                Prenota la Tua Escursione <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </section>
@endsection
