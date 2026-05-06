@extends('layouts.public')

@section('title', 'I nostri tour')
@section('meta_description', 'Scopri tutti i tour in catamarano disponibili: durata, prezzi e prossime partenze. Prenota online la tua escursione.')

@section('content')

    {{-- ============= BREADCRUMB ============= --}}
    <div class="tg-breadcrumb-area pt-150 pb-90 p-relative" style="background: linear-gradient(135deg, #0b3d5c 0%, #1a6da8 100%);">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center text-white">
                    <h1 class="mb-3 wow fadeInUp">I nostri tour</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Home</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Tour</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    {{-- ============= LISTING ============= --}}
    <div class="tg-listing-area pt-90 pb-100">
        <div class="container">

            {{-- Filter form --}}
            <form method="GET" action="{{ route('tours.index') }}" class="mb-4">
                <div class="row g-2 align-items-end bg-light rounded-4 p-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold mb-1">Data</label>
                        <input type="date" name="date" value="{{ $search['date'] }}" class="form-control" min="{{ now()->toDateString() }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold mb-1">Persone</label>
                        <input type="number" name="guests" value="{{ $search['guests'] ?: '' }}" class="form-control" min="1" max="50" placeholder="Numero ospiti">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold mb-1">Ordina per</label>
                        <select name="sort" class="form-select">
                            <option value="default" {{ $search['sort']==='default' ? 'selected' : '' }}>Predefinito</option>
                            <option value="price_asc" {{ $search['sort']==='price_asc' ? 'selected' : '' }}>Prezzo crescente</option>
                            <option value="price_desc" {{ $search['sort']==='price_desc' ? 'selected' : '' }}>Prezzo decrescente</option>
                            <option value="duration" {{ $search['sort']==='duration' ? 'selected' : '' }}>Durata</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-semibold">
                            <i class="fa-solid fa-magnifying-glass me-1"></i> Cerca
                        </button>
                    </div>
                </div>
            </form>

            @if($search['isSearch'])
                <p class="text-muted mb-3">
                    <strong>{{ $search['results'] }}</strong> tour trovati
                    @if($search['date']) per il {{ \Carbon\Carbon::parse($search['date'])->locale('it')->isoFormat('D MMM YYYY') }}@endif
                </p>
            @endif

            <div class="row">
                @forelse($tours as $i => $tour)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="tg-listing-card-item h-100">
                            <div class="tg-listing-card-thumb fix mb-15 p-relative">
                                <a href="{{ route('tours.show', $tour->slug) }}">
                                    @if($tour->primaryImage)
                                        <img class="tg-card-border w-100" src="{{ \Illuminate\Support\Facades\Storage::url($tour->primaryImage->path) }}" alt="{{ $tour->name }}" style="height:240px;object-fit:cover">
                                    @else
                                        <img class="tg-card-border w-100" src="{{ asset('assets/template/img/hero/hero-'.(($i % 5) + 1).'.jpg') }}" alt="{{ $tour->name }}" style="height:240px;object-fit:cover">
                                    @endif
                                </a>
                            </div>
                            <div class="tg-listing-card-content">
                                <h4 class="tg-listing-card-title">
                                    <a href="{{ route('tours.show', $tour->slug) }}">{{ $tour->name }}</a>
                                </h4>
                                @if($tour->description_short)
                                    <p class="text-muted small mb-2">{{ Str::limit($tour->description_short, 100) }}</p>
                                @endif
                                <div class="tg-listing-card-duration-tour mb-2">
                                    @if($tour->departure_point)
                                        <span class="tg-listing-card-duration-map mb-5 me-2">
                                            <i class="fa-solid fa-location-dot me-1"></i> {{ $tour->departure_point }}
                                        </span>
                                    @endif
                                    @if($tour->duration_hours)
                                        <span class="tg-listing-card-duration-time">
                                            <i class="fa-regular fa-clock me-1"></i> {{ $tour->duration_hours }}h
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="tg-listing-card-price d-flex align-items-end justify-content-between">
                                <div class="tg-listing-card-price-wrap price-bg d-flex align-items-center">
                                    <span class="tg-listing-card-currency-amount mr-5">
                                        <small class="me-1">da</small>
                                        <span class="currency-symbol">€</span>{{ number_format($tour->price_from ?? 0, 0, ',', '.') }}
                                    </span>
                                    <span class="tg-listing-card-activity-person">/persona</span>
                                </div>
                                <a href="{{ route('tours.show', $tour->slug) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                                    Dettagli
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fa-regular fa-face-frown fs-1 text-muted mb-3 d-block"></i>
                        <h4 class="text-muted">Nessun tour disponibile</h4>
                        <p class="text-muted">Prova a modificare i filtri di ricerca.</p>
                        <a href="{{ route('tours.index') }}" class="btn btn-outline-primary rounded-pill px-4">Reset filtri</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

@endsection
