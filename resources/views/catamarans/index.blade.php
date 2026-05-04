@extends('layouts.app')

@section('title', 'I Nostri Catamarani - Solarya Travel')

@section('content')
    {{-- Hero --}}
    <section class="bg-gradient-navy text-white py-5">
        <div class="container py-4">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3 font-serif">I Nostri Catamarani</h1>
                <p class="lead text-white-50 mb-0">
                    Scopri la nostra flotta di catamarani di lusso. Comfort, eleganza e prestazioni
                    per un'esperienza di navigazione indimenticabile.
                </p>
            </div>
        </div>
    </section>

    {{-- Search box --}}
    <section class="position-relative" style="margin-top:-2.5rem;z-index:10;">
        <div class="container">
            <div class="card border-0 shadow-lg rounded-4 p-4">
                <form method="GET" action="{{ route('catamarans.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="filter_date" class="form-label fw-semibold text-navy">Data escursione</label>
                        <input id="filter_date" type="date" name="date" required
                            value="{{ $search['date'] ?? '' }}"
                            min="{{ now()->addHours(config('booking.advance_hours', 24))->toDateString() }}"
                            class="form-control form-control-lg rounded-3">
                    </div>
                    <div class="col-md-2 col-6">
                        <label for="filter_adults" class="form-label fw-semibold text-navy">Adulti</label>
                        <input id="filter_adults" type="number" name="adults" min="1" max="20" required
                            value="{{ $search['adults'] ?? 2 }}" class="form-control form-control-lg rounded-3">
                    </div>
                    <div class="col-md-2 col-6">
                        <label for="filter_children" class="form-label fw-semibold text-navy">Bambini</label>
                        <input id="filter_children" type="number" name="children" min="0" max="20" required
                            value="{{ $search['children'] ?? 0 }}" class="form-control form-control-lg rounded-3">
                    </div>
                    <div class="col-md-2">
                        <label for="filter_slot_type" class="form-label fw-semibold text-navy">Durata</label>
                        <select id="filter_slot_type" name="slot_type" class="form-select form-select-lg rounded-3">
                            <option value="">Tutte</option>
                            <option value="half_day" @selected(($search['slot_type'] ?? null) === 'half_day')>Mezza giornata</option>
                            <option value="full_day" @selected(($search['slot_type'] ?? null) === 'full_day')>Giornata intera</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-gold btn-lg w-100 rounded-3 shadow-sm fw-semibold">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                @if(($search['isAvailabilitySearch'] ?? false) && !empty($search['date']))
                    <div class="mt-3 d-flex flex-wrap gap-2">
                        <span class="badge rounded-pill bg-primary-subtle text-primary fw-medium px-3 py-2">{{ $search['results'] }} catamarani disponibili</span>
                        <span class="badge rounded-pill bg-light text-secondary px-3 py-2">{{ \Carbon\Carbon::parse($search['date'])->locale('it')->isoFormat('D MMMM YYYY') }}</span>
                        <span class="badge rounded-pill bg-light text-secondary px-3 py-2">{{ $search['adults'] }} adulti</span>
                        <span class="badge rounded-pill bg-light text-secondary px-3 py-2">{{ $search['children'] }} bambini</span>
                        @if(!empty($search['slot_type']))
                            <span class="badge rounded-pill bg-light text-secondary px-3 py-2">{{ $search['slot_type'] === 'half_day' ? 'Mezza giornata' : 'Giornata intera' }}</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Catamarans Grid --}}
    <section class="py-5 bg-sand-50">
        <div class="container py-4">
            <div class="row g-4">
                @forelse($catamarans as $catamaran)
                    @php
                        $rawFeatures = $catamaran->features;
                        $features = is_string($rawFeatures) ? (json_decode($rawFeatures, true) ?? []) : (is_array($rawFeatures) ? $rawFeatures : []);
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <article class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-hover">
                            <a href="{{ route('catamarans.show', $catamaran) }}" class="d-block position-relative ratio ratio-4x3">
                                @if($catamaran->primaryImage)
                                    <img src="{{ asset('storage/' . $catamaran->primaryImage->image_path) }}"
                                         alt="{{ $catamaran->name }}" class="object-fit-cover w-100 h-100">
                                @else
                                    <div class="bg-gradient-primary d-flex align-items-center justify-content-center">
                                        <i class="bi bi-water display-1 text-white opacity-50"></i>
                                    </div>
                                @endif
                                <div class="position-absolute top-0 end-0 p-3 d-flex flex-column align-items-end gap-2">
                                    @if(($search['isAvailabilitySearch'] ?? false) && !empty($search['date']) && isset($catamaran->matched_seats_available))
                                        <span class="badge rounded-pill bg-success shadow-sm">Disponibile per {{ $catamaran->matched_seats_available }} persone</span>
                                    @endif
                                    <span class="badge rounded-pill bg-warning text-dark shadow-sm">Max {{ $catamaran->capacity }} ospiti</span>
                                </div>
                            </a>
                            <div class="card-body p-4">
                                <h2 class="h5 fw-bold text-navy mb-2">
                                    <a href="{{ route('catamarans.show', $catamaran) }}" class="text-decoration-none stretched-link-no text-navy">{{ $catamaran->name }}</a>
                                </h2>
                                <p class="text-secondary mb-3" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $catamaran->description_short }}</p>

                                <div class="d-flex flex-wrap gap-3 small text-muted mb-3">
                                    <span><i class="bi bi-rulers me-1"></i>{{ $catamaran->length_meters }}m</span>
                                    @foreach(array_slice($features, 0, 2) as $feature)
                                        <span><i class="bi bi-check-circle-fill text-warning me-1"></i>{{ $feature }}</span>
                                    @endforeach
                                </div>

                                <hr>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <small class="text-muted d-block">Da</small>
                                        <p class="h4 fw-bold text-primary mb-0">€{{ number_format($catamaran->price_per_person_half_day, 0) }}<small class="fw-normal text-muted fs-6">/persona</small></p>
                                    </div>
                                    @if(($search['isAvailabilitySearch'] ?? false) && !empty($search['date']))
                                        <a href="{{ route('booking.start', ['catamaran_slug' => $catamaran->slug, 'date' => $search['date']]) }}" class="btn btn-primary rounded-pill px-3 fw-semibold">Prenota <i class="bi bi-arrow-right ms-1"></i></a>
                                    @else
                                        <a href="{{ route('catamarans.show', $catamaran) }}" class="btn btn-primary rounded-pill px-3 fw-semibold">Scopri <i class="bi bi-arrow-right ms-1"></i></a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-water display-1 text-muted opacity-50 mb-3"></i>
                        <h3 class="h4 fw-semibold text-secondary mb-2">Nessun catamarano disponibile</h3>
                        <p class="text-muted">
                            @if($search['isAvailabilitySearch'] ?? false)
                                Nessuna disponibilità trovata per i criteri selezionati. Prova a cambiare data o numero ospiti.
                            @else
                                Torna a trovarci presto per scoprire la nostra flotta.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Why us --}}
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="display-6 fw-bold text-navy mb-3 font-serif">Perché Scegliere Solarya Travel</h2>
                <p class="lead text-secondary mx-auto" style="max-width:560px">Qualità, sicurezza e comfort per un'esperienza di navigazione senza paragoni</p>
            </div>
            <div class="row g-4">
                @php
                    $why = [
                        ['icon' => 'shield-check', 'bg' => 'primary', 'title' => 'Sicurezza Certificata', 'desc' => 'Tutti i nostri catamarani sono regolarmente ispezionati e certificati'],
                        ['icon' => 'stars', 'bg' => 'warning', 'title' => 'Equipaggio Esperto', 'desc' => 'Skipper professionisti con anni di esperienza nella navigazione'],
                        ['icon' => 'cash-coin', 'bg' => 'success', 'title' => 'Prezzi Trasparenti', 'desc' => 'Nessun costo nascosto, tutto incluso nel prezzo che vedi'],
                        ['icon' => 'gem', 'bg' => 'info', 'title' => 'Esperienza Premium', 'desc' => 'Servizio personalizzato per rendere ogni viaggio unico'],
                    ];
                @endphp
                @foreach($why as $w)
                    <div class="col-6 col-md-3 text-center">
                        <div class="rounded-4 mx-auto mb-3 d-flex align-items-center justify-content-center bg-{{ $w['bg'] }}-subtle text-{{ $w['bg'] }}" style="width:64px;height:64px;">
                            <i class="bi bi-{{ $w['icon'] }} fs-2"></i>
                        </div>
                        <h3 class="h6 fw-semibold text-navy mb-2">{{ $w['title'] }}</h3>
                        <p class="text-secondary small mb-0">{{ $w['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-5 bg-gradient-primary text-white">
        <div class="container py-4 text-center">
            <h2 class="display-6 fw-bold mb-3 font-serif">Pronto per Salpare?</h2>
            <p class="lead text-white-50 mb-4 mx-auto" style="max-width:560px">Scegli il catamarano perfetto per la tua avventura e prenota oggi stesso</p>
            <a href="{{ route('booking.start') }}" class="btn btn-light btn-lg rounded-pill shadow fw-semibold">
                Prenota Ora <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </section>
@endsection
