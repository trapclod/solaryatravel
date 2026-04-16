@extends('layouts.app')

@section('title', 'Chi Siamo - Solarya Travel')

@section('content')
    {{-- Hero Section --}}
    <section class="relative bg-gradient-to-r from-navy-900 to-navy-800 text-white py-20">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="max-w-3xl">
                <h1 class="text-4xl lg:text-5xl font-bold mb-4">Chi Siamo</h1>
                <p class="text-xl text-gray-300">
                    Passione per il mare, dedizione per l'eccellenza. 
                    Scopri la storia di Solarya Travel.
                </p>
            </div>
        </div>
    </section>

    {{-- Story Section --}}
    <section class="py-16 lg:py-24 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-navy-900 mb-6">La Nostra Storia</h2>
                    <div class="prose prose-lg text-gray-600">
                        <p>
                            Solarya Travel nasce dalla passione di un gruppo di amanti del mare che hanno deciso 
                            di condividere la bellezza delle coste italiane con viaggiatori da tutto il mondo.
                        </p>
                        <p>
                            Da oltre 15 anni offriamo escursioni in catamarano di alta qualità, combinando 
                            il comfort di imbarcazioni moderne con l'esperienza di skipper professionisti 
                            che conoscono ogni angolo nascosto della costa.
                        </p>
                        <p>
                            La nostra missione è semplice: creare esperienze indimenticabili in mare, 
                            dove ogni dettaglio è curato per garantire il massimo del relax e del piacere.
                        </p>
                    </div>
                </div>
                <div class="relative aspect-[4/3] rounded-2xl overflow-hidden shadow-lg">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center">
                        <svg class="w-24 h-24 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Values Section --}}
    <section class="py-16 lg:py-24 bg-sand-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-navy-900 mb-4">I Nostri Valori</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Ciò che ci guida ogni giorno nel nostro lavoro
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-8 shadow-sm text-center">
                    <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-navy-900 mb-3">Sicurezza</h3>
                    <p class="text-gray-600">
                        La sicurezza dei nostri ospiti è la nostra priorità assoluta. 
                        Tutte le imbarcazioni sono certificate e il nostro equipaggio è altamente qualificato.
                    </p>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-sm text-center">
                    <div class="w-16 h-16 bg-gold-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-navy-900 mb-3">Eccellenza</h3>
                    <p class="text-gray-600">
                        Ogni dettaglio conta. Dalla pulizia delle imbarcazioni alla qualità del cibo, 
                        cerchiamo sempre la perfezione.
                    </p>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-sm text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-navy-900 mb-3">Sostenibilità</h3>
                    <p class="text-gray-600">
                        Rispettiamo il mare che ci dà tanto. Utilizziamo pratiche eco-sostenibili 
                        e sensibilizziamo i nostri ospiti alla tutela dell'ambiente marino.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Team Section --}}
    <section class="py-16 lg:py-24 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-navy-900 mb-4">Il Nostro Team</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Professionisti appassionati pronti a rendere speciale la tua esperienza
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach(['Marco', 'Giulia', 'Alessandro', 'Francesca'] as $name)
                    <div class="text-center">
                        <div class="w-32 h-32 bg-gradient-to-br from-primary-100 to-primary-200 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-3xl font-bold text-primary-600">{{ substr($name, 0, 1) }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-navy-900">{{ $name }}</h3>
                        <p class="text-gray-500">Skipper</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-16 bg-gradient-to-r from-primary-600 to-primary-700 text-white">
        <div class="container mx-auto px-4 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold mb-4">Vuoi Conoscerci Meglio?</h2>
            <p class="text-xl text-primary-100 mb-8 max-w-2xl mx-auto">
                Contattaci per qualsiasi domanda o per prenotare la tua esperienza
            </p>
            <a href="{{ route('contact') }}" 
               class="inline-flex items-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-full hover:bg-primary-50 transition-colors shadow-lg">
                Contattaci
                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </section>
@endsection
