@extends('layouts.app')

@section('title', 'Esperienze - Solarya Travel')

@php
    $halfDay = [
        ['title' => 'Navigazione guidata', 'desc' => 'Skipper professionista con conoscenza del territorio'],
        ['title' => 'Aperitivo a bordo', 'desc' => 'Prosecco, vino locale e stuzzichini gourmet'],
        ['title' => 'Sosta per il bagno', 'desc' => 'In una baia esclusiva con acque cristalline'],
        ['title' => 'Attrezzatura snorkeling', 'desc' => 'Maschera, pinne e muta (su richiesta)'],
    ];
    $fullDay = [
        ['title' => 'Tutto della mezza giornata', 'desc' => 'Più molto altro ancora...'],
        ['title' => 'Pranzo gourmet a bordo', 'desc' => 'Cucina locale con ingredienti freschi e di stagione'],
        ['title' => 'Multiple soste per il bagno', 'desc' => 'Esplorazione di diverse baie e calette'],
        ['title' => 'Aperitivo al tramonto', 'desc' => 'Momento magico per concludere la giornata'],
    ];
@endphp

@section('content')
    <section class="bg-gradient-navy text-white py-5">
        <div class="container py-4">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3 font-serif">Le Nostre Esperienze</h1>
                <p class="lead text-white-50 mb-0">
                    Scopri le nostre escursioni esclusive in catamarano. Ogni esperienza è progettata
                    per offrirti momenti indimenticabili lungo le coste più belle.
                </p>
            </div>
        </div>
    </section>

    <section class="py-5 bg-sand-50">
        <div class="container py-4">

            {{-- Mezza Giornata --}}
            <div class="mb-5">
                <div class="text-center mb-5">
                    <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2 mb-3">Mezza Giornata</span>
                    <h2 class="display-6 fw-bold text-navy mb-3 font-serif">Escursione Mattina o Pomeriggio</h2>
                    <p class="lead text-secondary mx-auto" style="max-width:560px">4 ore di navigazione, perfette per un'esperienza intensa e memorabile</p>
                </div>

                <div class="row g-4 align-items-center">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow rounded-4 p-4 p-md-5">
                            <h3 class="h4 fw-bold text-navy mb-4 font-serif">Cosa Include</h3>
                            <ul class="list-unstyled mb-4">
                                @foreach($halfDay as $item)
                                    <li class="d-flex mb-3">
                                        <i class="bi bi-check-circle-fill text-warning me-3 mt-1"></i>
                                        <div>
                                            <strong class="text-navy d-block">{{ $item['title'] }}</strong>
                                            <span class="text-secondary small">{{ $item['desc'] }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <hr>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted">A partire da</small>
                                    <p class="h2 fw-bold text-primary mb-0">€85<span class="fs-6 fw-normal text-muted">/persona</span></p>
                                </div>
                                <a href="{{ route('booking.start') }}" class="btn btn-gold rounded-pill shadow-sm fw-semibold">Prenota Ora</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="ratio ratio-4x3 rounded-4 overflow-hidden shadow bg-gradient-primary">
                            <div class="d-flex align-items-center justify-content-center"><i class="bi bi-water text-white display-1 opacity-50"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Giornata Intera --}}
            <div class="mb-5">
                <div class="text-center mb-5">
                    <span class="badge rounded-pill bg-warning-subtle text-warning px-3 py-2 mb-3">Giornata Intera</span>
                    <h2 class="display-6 fw-bold text-navy mb-3 font-serif">Escursione Full Day</h2>
                    <p class="lead text-secondary mx-auto" style="max-width:560px">8 ore di pura magia, con pranzo gourmet a bordo e molteplici soste</p>
                </div>

                <div class="row g-4 align-items-center">
                    <div class="col-lg-6 order-lg-1 order-2">
                        <div class="ratio ratio-4x3 rounded-4 overflow-hidden shadow bg-gradient-gold">
                            <div class="d-flex align-items-center justify-content-center"><i class="bi bi-sun text-white display-1 opacity-50"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-2 order-1">
                        <div class="card border-0 shadow rounded-4 p-4 p-md-5">
                            <h3 class="h4 fw-bold text-navy mb-4 font-serif">Cosa Include</h3>
                            <ul class="list-unstyled mb-4">
                                @foreach($fullDay as $item)
                                    <li class="d-flex mb-3">
                                        <i class="bi bi-check-circle-fill text-warning me-3 mt-1"></i>
                                        <div>
                                            <strong class="text-navy d-block">{{ $item['title'] }}</strong>
                                            <span class="text-secondary small">{{ $item['desc'] }}</span>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            <hr>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted">A partire da</small>
                                    <p class="h2 fw-bold text-primary mb-0">€150<span class="fs-6 fw-normal text-muted">/persona</span></p>
                                </div>
                                <a href="{{ route('booking.start') }}" class="btn btn-gold rounded-pill shadow-sm fw-semibold">Prenota Ora</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Privata --}}
            <div class="rounded-4 p-4 p-md-5 bg-gradient-navy text-white shadow-lg">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-6">
                        <span class="badge rounded-pill bg-warning text-dark px-3 py-2 mb-3">Esclusiva</span>
                        <h2 class="display-6 fw-bold mb-3 font-serif">Esperienza Privata</h2>
                        <p class="lead text-white-50 mb-4">
                            Il catamarano tutto per te e i tuoi ospiti. Perfetto per eventi speciali,
                            celebrazioni o semplicemente per chi desidera la massima privacy.
                        </p>
                        <ul class="list-unstyled mb-4">
                            @foreach(['Itinerario personalizzato', 'Menu su misura', 'Servizio dedicato', 'Perfetto per eventi e celebrazioni'] as $f)
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-warning me-2"></i>{{ $f }}</li>
                            @endforeach
                        </ul>
                        <a href="{{ route('booking.start') }}" class="btn btn-gold btn-lg rounded-pill shadow fw-semibold">
                            Richiedi un Preventivo <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                    <div class="col-lg-6">
                        <div class="ratio ratio-1x1 rounded-4 overflow-hidden d-flex align-items-center justify-content-center" style="background:rgba(234,179,8,.08);">
                            <div class="d-flex align-items-center justify-content-center"><i class="bi bi-stars display-1 text-warning opacity-50"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-gradient-primary text-white">
        <div class="container py-4 text-center">
            <h2 class="display-6 fw-bold mb-3 font-serif">Pronto a Vivere un'Esperienza Unica?</h2>
            <p class="lead text-white-50 mb-4 mx-auto" style="max-width:560px">Scegli il catamarano e l'esperienza perfetta per te</p>
            <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                <a href="{{ route('catamarans.index') }}" class="btn btn-outline-light btn-lg rounded-pill fw-semibold">Scopri i Catamarani</a>
                <a href="{{ route('booking.start') }}" class="btn btn-light btn-lg rounded-pill shadow fw-semibold">
                    Prenota Subito <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>
@endsection
