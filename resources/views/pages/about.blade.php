@extends('layouts.app')

@section('title', 'Chi Siamo - Solarya Travel')

@section('content')
    {{-- Hero --}}
    <section class="bg-gradient-navy text-white py-5">
        <div class="container py-4">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3 font-serif">Chi Siamo</h1>
                <p class="lead text-white-50 mb-0">
                    Passione per il mare, dedizione per l'eccellenza.
                    Scopri la storia di Solarya Travel.
                </p>
            </div>
        </div>
    </section>

    {{-- Story --}}
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <h2 class="h2 fw-bold text-navy mb-4 font-serif">La Nostra Storia</h2>
                    <p class="text-secondary">
                        Solarya Travel nasce dalla passione di un gruppo di amanti del mare che hanno deciso
                        di condividere la bellezza delle coste italiane con viaggiatori da tutto il mondo.
                    </p>
                    <p class="text-secondary">
                        Da oltre 15 anni offriamo escursioni in catamarano di alta qualità, combinando
                        il comfort di imbarcazioni moderne con l'esperienza di skipper professionisti
                        che conoscono ogni angolo nascosto della costa.
                    </p>
                    <p class="text-secondary">
                        La nostra missione è semplice: creare esperienze indimenticabili in mare,
                        dove ogni dettaglio è curato per garantire il massimo del relax e del piacere.
                    </p>
                </div>
                <div class="col-lg-6">
                    <div class="ratio ratio-4x3 rounded-4 overflow-hidden shadow bg-gradient-primary d-flex align-items-center justify-content-center">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-water text-white display-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Values --}}
    <section class="py-5 bg-sand-50">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="h2 fw-bold text-navy mb-3 font-serif">I Nostri Valori</h2>
                <p class="lead text-secondary mx-auto" style="max-width:560px">Ciò che ci guida ogni giorno nel nostro lavoro</p>
            </div>

            <div class="row g-4">
                @php
                    $values = [
                        ['icon' => 'shield-check', 'bg' => 'primary', 'title' => 'Sicurezza', 'desc' => "La sicurezza dei nostri ospiti è la nostra priorità assoluta. Tutte le imbarcazioni sono certificate e il nostro equipaggio è altamente qualificato."],
                        ['icon' => 'stars', 'bg' => 'warning', 'title' => 'Eccellenza', 'desc' => "Ogni dettaglio conta. Dalla pulizia delle imbarcazioni alla qualità del cibo, cerchiamo sempre la perfezione."],
                        ['icon' => 'globe-europe-africa', 'bg' => 'success', 'title' => 'Sostenibilità', 'desc' => "Rispettiamo il mare che ci dà tanto. Utilizziamo pratiche eco-sostenibili e sensibilizziamo i nostri ospiti alla tutela dell'ambiente marino."],
                    ];
                @endphp
                @foreach($values as $v)
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 text-center p-4">
                            <div class="mx-auto mb-3 rounded-4 bg-{{ $v['bg'] }}-subtle text-{{ $v['bg'] }} d-flex align-items-center justify-content-center" style="width:64px;height:64px;">
                                <i class="bi bi-{{ $v['icon'] }} fs-2"></i>
                            </div>
                            <h3 class="h5 fw-bold text-navy mb-2">{{ $v['title'] }}</h3>
                            <p class="text-secondary mb-0">{{ $v['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Team --}}
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="h2 fw-bold text-navy mb-3 font-serif">Il Nostro Team</h2>
                <p class="lead text-secondary mx-auto" style="max-width:560px">Professionisti appassionati pronti a rendere speciale la tua esperienza</p>
            </div>

            <div class="row g-4">
                @foreach(['Marco', 'Giulia', 'Alessandro', 'Francesca'] as $name)
                    <div class="col-6 col-md-3 text-center">
                        <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-gradient-primary text-white fw-bold" style="width:128px;height:128px;font-size:2rem;">
                            {{ substr($name, 0, 1) }}
                        </div>
                        <h3 class="h6 fw-bold text-navy mb-1">{{ $name }}</h3>
                        <p class="text-muted small mb-0">Skipper</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-5 bg-gradient-primary text-white">
        <div class="container py-4 text-center">
            <h2 class="display-6 fw-bold mb-3 font-serif">Vuoi Conoscerci Meglio?</h2>
            <p class="lead mb-4 mx-auto text-white-50" style="max-width:560px">Contattaci per qualsiasi domanda o per prenotare la tua esperienza</p>
            <a href="{{ route('contact') }}" class="btn btn-light btn-lg rounded-pill shadow-sm fw-semibold">
                Contattaci <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </section>
@endsection
