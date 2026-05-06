@extends('layouts.public')

@section('title', $tour->meta_title ?: $tour->name)
@section('meta_description', $tour->meta_description ?: ($tour->description_short ?: ''))

@section('content')

    {{-- ============= HERO ============= --}}
    <div class="tg-breadcrumb-area pt-150 pb-90 p-relative" style="
        @if($tour->primaryImage)
            background: linear-gradient(rgba(11,61,92,.6),rgba(11,61,92,.6)), url('{{ \Illuminate\Support\Facades\Storage::url($tour->primaryImage->path) }}') center/cover;
        @else
            background: linear-gradient(135deg, #0b3d5c 0%, #1a6da8 100%);
        @endif
    ">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center text-white">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb justify-content-center mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Home</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tours.index') }}" class="text-white-50 text-decoration-none">Tour</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">{{ $tour->name }}</li>
                        </ol>
                    </nav>
                    <h1 class="mb-3 wow fadeInUp">{{ $tour->name }}</h1>
                    @if($tour->description_short)
                        <p class="lead mb-0 wow fadeInUp" style="max-width:700px;margin:0 auto;">{{ $tour->description_short }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ============= CONTENT ============= --}}
    <div class="py-5">
        <div class="container">
            <div class="row g-4">
                {{-- LEFT --}}
                <div class="col-lg-8">

                    {{-- Quick info --}}
                    <div class="bg-light rounded-4 p-4 mb-4">
                        <div class="row text-center g-3">
                            @if($tour->duration_hours)
                                <div class="col">
                                    <i class="fa-regular fa-clock fs-3 text-primary mb-2"></i>
                                    <div class="small text-muted">Durata</div>
                                    <strong>{{ $tour->duration_hours }} ore</strong>
                                </div>
                            @endif
                            <div class="col">
                                <i class="fa-regular fa-user fs-3 text-primary mb-2"></i>
                                <div class="small text-muted">Posti</div>
                                <strong>fino a {{ $tour->max_capacity }}</strong>
                            </div>
                            @if($tour->departure_point)
                                <div class="col">
                                    <i class="fa-solid fa-location-dot fs-3 text-primary mb-2"></i>
                                    <div class="small text-muted">Partenza</div>
                                    <strong>{{ $tour->departure_point }}</strong>
                                </div>
                            @endif
                            <div class="col">
                                <i class="fa-solid fa-tag fs-3 text-primary mb-2"></i>
                                <div class="small text-muted">A partire da</div>
                                <strong>€{{ number_format($tour->price_from ?? 0, 0, ',', '.') }}/pers</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Description --}}
                    @if($tour->description)
                        <div class="mb-4">
                            <h3 class="mb-3">Descrizione</h3>
                            <div class="text-secondary lh-lg">{!! nl2br(e($tour->description)) !!}</div>
                        </div>
                    @endif

                    {{-- Itinerario --}}
                    @if($tour->itinerary)
                        <div class="mb-4">
                            <h3 class="mb-3"><i class="fa-solid fa-route text-primary me-2"></i>Itinerario</h3>
                            <div class="bg-white border rounded-4 p-4">
                                <div class="text-secondary lh-lg">{!! nl2br(e($tour->itinerary)) !!}</div>
                            </div>
                        </div>
                    @endif

                    {{-- Included / Excluded --}}
                    @if(!empty($tour->included) || !empty($tour->excluded))
                        <div class="row g-3 mb-4">
                            @if(!empty($tour->included))
                                <div class="col-md-6">
                                    <div class="bg-success-subtle rounded-4 p-4 h-100">
                                        <h5 class="text-success mb-3"><i class="fa-solid fa-check-circle me-2"></i>Incluso</h5>
                                        <ul class="list-unstyled mb-0">
                                            @foreach((array) $tour->included as $item)
                                                <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                            @if(!empty($tour->excluded))
                                <div class="col-md-6">
                                    <div class="bg-danger-subtle rounded-4 p-4 h-100">
                                        <h5 class="text-danger mb-3"><i class="fa-solid fa-circle-xmark me-2"></i>Non incluso</h5>
                                        <ul class="list-unstyled mb-0">
                                            @foreach((array) $tour->excluded as $item)
                                                <li class="mb-2"><i class="fa-solid fa-xmark text-danger me-2"></i>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Fasce d'età / prezzi --}}
                    @if($tour->ageBrackets->count())
                        <div class="mb-4">
                            <h3 class="mb-3"><i class="fa-solid fa-tags text-primary me-2"></i>Tariffe per fascia d'età</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Fascia</th>
                                            <th>Età</th>
                                            <th class="text-end">Prezzo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tour->ageBrackets as $b)
                                            <tr>
                                                <td><strong>{{ $b->label }}</strong></td>
                                                <td class="text-muted small">
                                                    @if($b->max_age === null)
                                                        da {{ $b->min_age }} anni
                                                    @else
                                                        {{ $b->min_age }} - {{ $b->max_age }} anni
                                                    @endif
                                                </td>
                                                <td class="text-end fw-bold">€{{ number_format($b->price, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- RIGHT (departures sidebar) --}}
                <div class="col-lg-4">
                    <div class="position-sticky" style="top:90px">
                        <div class="bg-white border rounded-4 shadow-sm p-4 mb-3">
                            <h5 class="mb-3"><i class="fa-regular fa-calendar text-primary me-2"></i>Prossime partenze</h5>

                            @if($departures->count())
                                <div class="d-flex flex-column gap-2" style="max-height:400px;overflow-y:auto">
                                    @foreach($departures->take(15) as $dep)
                                        <a href="{{ route('booking.start', ['tour' => $tour->slug, 'departure' => $dep->id]) }}"
                                           class="d-flex justify-content-between align-items-center p-2 border rounded-3 text-decoration-none text-dark hover-bg-light">
                                            <div>
                                                <div class="fw-semibold small">
                                                    {{ \Carbon\Carbon::parse($dep->departure_date)->locale('it')->isoFormat('ddd D MMM') }}
                                                </div>
                                                <div class="text-muted small">
                                                    {{ \Carbon\Carbon::parse($dep->start_time)->format('H:i') }}@if($dep->end_time) - {{ \Carbon\Carbon::parse($dep->end_time)->format('H:i') }}@endif
                                                </div>
                                            </div>
                                            <i class="fa-solid fa-chevron-right text-muted small"></i>
                                        </a>
                                    @endforeach
                                </div>
                                <a href="{{ route('booking.start', ['tour' => $tour->slug]) }}"
                                   class="btn btn-primary w-100 rounded-pill fw-semibold mt-3">
                                    <i class="fa-solid fa-ticket me-2"></i>Prenota ora
                                </a>
                            @else
                                <p class="text-muted small mb-0">Nessuna partenza programmata nei prossimi 60 giorni.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Similar tours --}}
            @if($similar->count())
                <div class="mt-5 pt-4 border-top">
                    <h3 class="mb-4">Tour simili</h3>
                    <div class="row g-3">
                        @foreach($similar as $i => $st)
                            <div class="col-lg-4 col-md-6">
                                <a href="{{ route('tours.show', $st->slug) }}" class="text-decoration-none">
                                    <div class="bg-white border rounded-4 overflow-hidden h-100 shadow-sm">
                                        @if($st->primaryImage)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($st->primaryImage->path) }}" class="w-100" alt="{{ $st->name }}" style="height:180px;object-fit:cover">
                                        @else
                                            <img src="{{ asset('assets/template/img/hero/hero-'.(($i % 5) + 1).'.jpg') }}" class="w-100" alt="{{ $st->name }}" style="height:180px;object-fit:cover">
                                        @endif
                                        <div class="p-3">
                                            <h6 class="text-dark mb-1">{{ $st->name }}</h6>
                                            <div class="d-flex justify-content-between text-muted small">
                                                <span>@if($st->duration_hours) {{ $st->duration_hours }}h @endif</span>
                                                <strong class="text-primary">da €{{ number_format($st->price_from ?? 0, 0, ',', '.') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
