@extends('layouts.app')

@section('title', 'Escursioni in Catamarano di Lusso')
@section('meta_description', 'Scopri le esperienze esclusive in catamarano con Solarya Travel. Escursioni mezza giornata, giornata intera e charter privati lungo la costa.')

@section('content')
    <!-- Hero Section -->
    <section class="relative h-screen min-h-[600px] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-navy-900/80 to-navy-900/40 z-10"></div>
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/images/hero-catamaran.jpg');"></div>
        
        <div class="relative z-20 text-center text-white px-4 max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-serif font-bold mb-6 animate-fade-in">
                Vivi il Mare come Mai Prima
            </h1>
            <p class="text-xl md:text-2xl text-gray-200 mb-8 max-w-2xl mx-auto">
                Escursioni esclusive in catamarano lungo le coste più belle. 
                Comfort, eleganza e servizio impeccabile per un'esperienza indimenticabile.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('booking.start') }}" 
                   class="inline-flex items-center px-8 py-4 bg-gold-500 text-white font-semibold rounded-full hover:bg-gold-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    Prenota Ora
                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
                <a href="{{ route('catamarans.index') }}" 
                   class="inline-flex items-center px-8 py-4 border-2 border-white text-white font-semibold rounded-full hover:bg-white hover:text-navy-900 transition-all duration-300">
                    Scopri i Catamarani
                </a>
            </div>
        </div>

        <!-- Scroll indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20 animate-bounce">
            <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-navy-900 mb-4">
                    Un'Esperienza Senza Pari
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Ogni dettaglio è curato per offrirti momenti di puro relax e meraviglia
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-navy-900 mb-2">Lusso Accessibile</h3>
                    <p class="text-gray-600">Catamarani di ultima generazione con ogni comfort a bordo</p>
                </div>

                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-navy-900 mb-2">Equipaggio Esperto</h3>
                    <p class="text-gray-600">Skipper professionisti e staff dedicato al tuo benessere</p>
                </div>

                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-navy-900 mb-2">Flessibilità Totale</h3>
                    <p class="text-gray-600">Mezza giornata, giornata intera o escursione privata</p>
                </div>

                <div class="text-center p-6">
                    <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-navy-900 mb-2">Prenotazione Facile</h3>
                    <p class="text-gray-600">Sistema di booking online sicuro e conferma immediata</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Catamarans Section -->
    <section class="py-20 bg-sand-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-navy-900 mb-4">
                    La Nostra Flotta
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Scegli il catamarano perfetto per la tua avventura
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($catamarans as $catamaran)
                    <x-card hover>
                        <div class="aspect-[4/3] overflow-hidden rounded-t-xl -mx-6 -mt-6 mb-4">
                            @if($catamaran->primaryImage)
                                <img src="{{ $catamaran->primaryImage->url }}" 
                                     alt="{{ $catamaran->name }}"
                                     class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <h3 class="text-xl font-semibold text-navy-900 mb-2">{{ $catamaran->name }}</h3>
                        <p class="text-gray-600 text-sm mb-4">{{ $catamaran->description_short }}</p>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                {{ $catamaran->capacity }} ospiti
                            </span>
                            @if($catamaran->length_meters)
                                <span>{{ $catamaran->length_meters }}m</span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t">
                            <div>
                                <span class="text-sm text-gray-500">da</span>
                                <span class="text-2xl font-bold text-primary-600">€{{ number_format($catamaran->price_per_person_half_day, 0) }}</span>
                                <span class="text-sm text-gray-500">/persona</span>
                            </div>
                            <a href="{{ route('catamarans.show', $catamaran->slug) }}" 
                               class="inline-flex items-center text-primary-600 font-medium hover:text-primary-700">
                                Scopri
                                <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </x-card>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('catamarans.index') }}" 
                   class="inline-flex items-center px-8 py-3 bg-navy-900 text-white font-semibold rounded-full hover:bg-navy-800 transition-colors">
                    Vedi Tutti i Catamarani
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 bg-navy-900 text-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl md:text-5xl font-bold text-gold-400 mb-2">{{ number_format($stats['happy_guests']) }}+</div>
                    <div class="text-gray-300">Ospiti Felici</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-bold text-gold-400 mb-2">{{ $stats['years_experience'] }}</div>
                    <div class="text-gray-300">Anni di Esperienza</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-bold text-gold-400 mb-2">{{ number_format($stats['excursions']) }}+</div>
                    <div class="text-gray-300">Escursioni</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-bold text-gold-400 mb-2">{{ $stats['catamarans'] }}</div>
                    <div class="text-gray-300">Catamarani</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-serif font-bold text-navy-900 mb-4">
                    Cosa Dicono i Nostri Ospiti
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Storie ed esperienze di chi ha vissuto l'avventura Solarya
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($testimonials as $testimonial)
                    <x-card>
                        <div class="flex items-center mb-4">
                            @for($i = 0; $i < $testimonial['rating']; $i++)
                                <svg class="w-5 h-5 text-gold-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-gray-600 mb-4 italic">"{{ $testimonial['text'] }}"</p>
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                <span class="text-primary-600 font-semibold">{{ substr($testimonial['name'], 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="font-semibold text-navy-900">{{ $testimonial['name'] }}</div>
                                <div class="text-sm text-gray-500">{{ $testimonial['location'] }}</div>
                            </div>
                        </div>
                    </x-card>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-primary-600 to-primary-700 text-white">
        <div class="container mx-auto px-4 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-serif font-bold mb-4">
                Pronto per l'Avventura?
            </h2>
            <p class="text-xl text-primary-100 mb-8 max-w-2xl mx-auto">
                Prenota oggi la tua escursione in catamarano e vivi un'esperienza indimenticabile
            </p>
            <a href="{{ route('booking.start') }}" 
               class="inline-flex items-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-full hover:bg-primary-50 transition-colors shadow-lg">
                Prenota la Tua Escursione
                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </section>
@endsection
