@extends('layouts.app')

@section('title', 'Esperienze - Solarya Travel')

@section('content')
    {{-- Hero Section --}}
    <section class="relative bg-gradient-to-r from-navy-900 to-navy-800 text-white py-20">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="max-w-3xl">
                <h1 class="text-4xl lg:text-5xl font-bold mb-4">Le Nostre Esperienze</h1>
                <p class="text-xl text-gray-300">
                    Scopri le nostre escursioni esclusive in catamarano. Ogni esperienza è progettata 
                    per offrirti momenti indimenticabili lungo le coste più belle.
                </p>
            </div>
        </div>
    </section>

    {{-- Experiences Grid --}}
    <section class="py-16 lg:py-24 bg-sand-50">
        <div class="container mx-auto px-4 lg:px-8">
            {{-- Half Day Experience --}}
            <div class="mb-16">
                <div class="text-center mb-12">
                    <span class="inline-block px-4 py-2 bg-primary-100 text-primary-700 rounded-full text-sm font-semibold mb-4">
                        Mezza Giornata
                    </span>
                    <h2 class="text-3xl lg:text-4xl font-bold text-navy-900 mb-4">Escursione Mattina o Pomeriggio</h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        4 ore di navigazione, perfette per un'esperienza intensa e memorabile
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h3 class="text-2xl font-bold text-navy-900 mb-6">Cosa Include</h3>
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-gold-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <strong class="text-navy-900">Navigazione guidata</strong>
                                    <p class="text-gray-600 text-sm">Skipper professionista con conoscenza del territorio</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-gold-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <strong class="text-navy-900">Aperitivo a bordo</strong>
                                    <p class="text-gray-600 text-sm">Prosecco, vino locale e stuzzichini gourmet</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-gold-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <strong class="text-navy-900">Sosta per il bagno</strong>
                                    <p class="text-gray-600 text-sm">In una baia esclusiva con acque cristalline</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-gold-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <strong class="text-navy-900">Attrezzatura snorkeling</strong>
                                    <p class="text-gray-600 text-sm">Maschera, pinne e muta (su richiesta)</p>
                                </div>
                            </li>
                        </ul>
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-sm text-gray-500">A partire da</span>
                                    <p class="text-3xl font-bold text-primary-600">€85<span class="text-lg font-normal text-gray-500">/persona</span></p>
                                </div>
                                <a href="{{ route('booking.start') }}" class="inline-flex items-center px-6 py-3 bg-gold-500 text-white font-semibold rounded-lg hover:bg-gold-600 transition-colors">
                                    Prenota Ora
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="relative aspect-[4/3] rounded-2xl overflow-hidden shadow-lg">
                        <div class="absolute inset-0 bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center">
                            <svg class="w-24 h-24 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Full Day Experience --}}
            <div class="mb-16">
                <div class="text-center mb-12">
                    <span class="inline-block px-4 py-2 bg-gold-100 text-gold-700 rounded-full text-sm font-semibold mb-4">
                        Giornata Intera
                    </span>
                    <h2 class="text-3xl lg:text-4xl font-bold text-navy-900 mb-4">Escursione Full Day</h2>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        8 ore di pura magia, con pranzo gourmet a bordo e molteplici soste
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <div class="order-2 lg:order-1 relative aspect-[4/3] rounded-2xl overflow-hidden shadow-lg">
                        <div class="absolute inset-0 bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center">
                            <svg class="w-24 h-24 text-white/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>

                    <div class="order-1 lg:order-2 bg-white rounded-2xl shadow-lg p-8">
                        <h3 class="text-2xl font-bold text-navy-900 mb-6">Cosa Include</h3>
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-gold-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <strong class="text-navy-900">Tutto della mezza giornata</strong>
                                    <p class="text-gray-600 text-sm">Più molto altro ancora...</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-gold-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <strong class="text-navy-900">Pranzo gourmet a bordo</strong>
                                    <p class="text-gray-600 text-sm">Cucina locale con ingredienti freschi e di stagione</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-gold-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <strong class="text-navy-900">Multiple soste per il bagno</strong>
                                    <p class="text-gray-600 text-sm">Esplorazione di diverse baie e calette</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-gold-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <strong class="text-navy-900">Aperitivo al tramonto</strong>
                                    <p class="text-gray-600 text-sm">Momento magico per concludere la giornata</p>
                                </div>
                            </li>
                        </ul>
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-sm text-gray-500">A partire da</span>
                                    <p class="text-3xl font-bold text-primary-600">€150<span class="text-lg font-normal text-gray-500">/persona</span></p>
                                </div>
                                <a href="{{ route('booking.start') }}" class="inline-flex items-center px-6 py-3 bg-gold-500 text-white font-semibold rounded-lg hover:bg-gold-600 transition-colors">
                                    Prenota Ora
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Private Experience --}}
            <div class="bg-gradient-to-r from-navy-900 to-navy-800 rounded-3xl p-8 lg:p-12 text-white">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                    <div>
                        <span class="inline-block px-4 py-2 bg-gold-500 text-navy-900 rounded-full text-sm font-semibold mb-4">
                            Esclusiva
                        </span>
                        <h2 class="text-3xl lg:text-4xl font-bold mb-4">Esperienza Privata</h2>
                        <p class="text-xl text-gray-300 mb-6">
                            Il catamarano tutto per te e i tuoi ospiti. Perfetto per eventi speciali, 
                            celebrazioni o semplicemente per chi desidera la massima privacy.
                        </p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-gold-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Itinerario personalizzato
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-gold-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Menu su misura
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-gold-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Servizio dedicato
                            </li>
                            <li class="flex items-center">
                                <svg class="w-5 h-5 text-gold-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Perfetto per eventi e celebrazioni
                            </li>
                        </ul>
                        <a href="{{ route('booking.start') }}" class="inline-flex items-center px-8 py-4 bg-gold-500 text-navy-900 font-semibold rounded-xl hover:bg-gold-400 transition-colors">
                            Richiedi un Preventivo
                            <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>
                    <div class="relative aspect-square rounded-2xl overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-gold-400/20 to-gold-600/20 flex items-center justify-center">
                            <svg class="w-32 h-32 text-gold-400/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-16 bg-gradient-to-r from-primary-600 to-primary-700 text-white">
        <div class="container mx-auto px-4 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold mb-4">Pronto a Vivere un'Esperienza Unica?</h2>
            <p class="text-xl text-primary-100 mb-8 max-w-2xl mx-auto">
                Scegli il catamarano e l'esperienza perfetta per te
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('catamarans.index') }}" 
                   class="inline-flex items-center px-8 py-4 border-2 border-white text-white font-semibold rounded-full hover:bg-white hover:text-primary-600 transition-colors">
                    Scopri i Catamarani
                </a>
                <a href="{{ route('booking.start') }}" 
                   class="inline-flex items-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-full hover:bg-primary-50 transition-colors shadow-lg">
                    Prenota Subito
                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </div>
        </div>
    </section>
@endsection
