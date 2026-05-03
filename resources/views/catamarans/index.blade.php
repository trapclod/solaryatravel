@extends('layouts.app')

@section('title', 'I Nostri Catamarani - Solarya Travel')

@section('content')
    {{-- Hero Section --}}
    <section class="relative bg-gradient-to-r from-navy-900 to-navy-800 text-white py-20">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="max-w-3xl">
                <h1 class="text-4xl lg:text-5xl font-bold mb-4">I Nostri Catamarani</h1>
                <p class="text-xl text-gray-300">
                    Scopri la nostra flotta di catamarani di lusso. Comfort, eleganza e prestazioni 
                    per un'esperienza di navigazione indimenticabile.
                </p>
            </div>
        </div>
    </section>

    {{-- Availability Search --}}
    <section class="relative -mt-10 z-10">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="bg-white rounded-3xl shadow-xl border border-sand-200 p-5 lg:p-6">
                <form method="GET" action="{{ route('catamarans.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 md:gap-4 items-end">
                    <div class="md:col-span-5">
                        <label for="filter_date" class="block text-sm font-semibold text-navy-900 mb-2">Data escursione</label>
                        <input
                            id="filter_date"
                            type="date"
                            name="date"
                            value="{{ $search['date'] ?? '' }}"
                            min="{{ now()->addHours(config('booking.advance_hours', 24))->toDateString() }}"
                            required
                            class="w-full rounded-xl border border-sand-300 bg-white text-navy-900 px-4 py-3 focus:ring-2 focus:ring-gold-400 focus:border-gold-400"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label for="filter_adults" class="block text-sm font-semibold text-navy-900 mb-2">Adulti</label>
                        <input
                            id="filter_adults"
                            type="number"
                            name="adults"
                            min="1"
                            max="20"
                            value="{{ $search['adults'] ?? 2 }}"
                            required
                            class="w-full rounded-xl border border-sand-300 bg-white text-navy-900 px-4 py-3 focus:ring-2 focus:ring-gold-400 focus:border-gold-400"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label for="filter_children" class="block text-sm font-semibold text-navy-900 mb-2">Bambini</label>
                        <input
                            id="filter_children"
                            type="number"
                            name="children"
                            min="0"
                            max="20"
                            value="{{ $search['children'] ?? 0 }}"
                            required
                            class="w-full rounded-xl border border-sand-300 bg-white text-navy-900 px-4 py-3 focus:ring-2 focus:ring-gold-400 focus:border-gold-400"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label for="filter_slot_type" class="block text-sm font-semibold text-navy-900 mb-2">Durata</label>
                        <select
                            id="filter_slot_type"
                            name="slot_type"
                            class="w-full rounded-xl border border-sand-300 bg-white text-navy-900 px-4 py-3 focus:ring-2 focus:ring-gold-400 focus:border-gold-400"
                        >
                            <option value="">Tutte</option>
                            <option value="half_day" @selected(($search['slot_type'] ?? null) === 'half_day')>Mezza giornata</option>
                            <option value="full_day" @selected(($search['slot_type'] ?? null) === 'full_day')>Giornata intera</option>
                        </select>
                    </div>

                    <div class="md:col-span-1">
                        <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-3 bg-gradient-to-r from-gold-500 to-gold-600 text-white font-semibold rounded-xl hover:from-gold-600 hover:to-gold-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                            Cerca
                        </button>
                    </div>
                </form>

                @if(($search['isAvailabilitySearch'] ?? false) && !empty($search['date']))
                    <div class="mt-4 flex flex-wrap items-center gap-2 text-sm">
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-primary-50 text-primary-700 font-medium">
                            {{ $search['results'] }} catamarani disponibili
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-sand-100 text-navy-700">
                            {{ \Carbon\Carbon::parse($search['date'])->locale('it')->isoFormat('D MMMM YYYY') }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-sand-100 text-navy-700">
                            {{ $search['adults'] }} adulti
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-sand-100 text-navy-700">
                            {{ $search['children'] }} bambini
                        </span>
                        @if(!empty($search['slot_type']))
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-sand-100 text-navy-700">
                                {{ $search['slot_type'] === 'half_day' ? 'Mezza giornata' : 'Giornata intera' }}
                            </span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Catamarans Grid --}}
    <section class="py-16 lg:py-24 bg-sand-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($catamarans as $catamaran)
                    <article class="bg-white rounded-2xl shadow-lg overflow-hidden group hover:shadow-xl transition-shadow duration-300">
                        {{-- Image --}}
                        <a href="{{ route('catamarans.show', $catamaran) }}" class="block relative aspect-[4/3] overflow-hidden">
                            @if($catamaran->primaryImage)
                                <img src="{{ asset('storage/' . $catamaran->primaryImage->image_path) }}" 
                                     alt="{{ $catamaran->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center">
                                    <svg class="w-16 h-16 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute top-4 right-4 flex flex-col gap-2 items-end">
                                @if(($search['isAvailabilitySearch'] ?? false) && !empty($search['date']) && isset($catamaran->matched_seats_available))
                                    <span class="bg-green-600 text-white text-xs font-semibold px-3 py-1 rounded-full shadow">
                                        Disponibile per {{ $catamaran->matched_seats_available }} persone
                                    </span>
                                @endif
                                <span class="bg-gold-500 text-white text-sm font-semibold px-3 py-1 rounded-full">
                                    Max {{ $catamaran->capacity }} ospiti
                                </span>
                            </div>
                        </a>

                        {{-- Content --}}
                        <div class="p-6">
                            <h2 class="text-xl font-bold text-navy-900 mb-2">
                                <a href="{{ route('catamarans.show', $catamaran) }}" class="hover:text-primary-600 transition-colors">
                                    {{ $catamaran->name }}
                                </a>
                            </h2>
                            
                            <p class="text-gray-600 mb-4 line-clamp-2">
                                {{ $catamaran->description_short }}
                            </p>

                            {{-- Features --}}
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="inline-flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                    </svg>
                                    {{ $catamaran->length_meters }}m
                                </span>
                                @php 
                                    $rawFeatures = $catamaran->features;
                                    if (is_string($rawFeatures)) {
                                        $features = json_decode($rawFeatures, true) ?? [];
                                    } else {
                                        $features = is_array($rawFeatures) ? $rawFeatures : [];
                                    }
                                @endphp
                                @foreach(array_slice($features, 0, 2) as $feature)
                                    <span class="inline-flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-1 text-gold-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $feature }}
                                    </span>
                                @endforeach
                            </div>

                            {{-- Price & CTA --}}
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div>
                                    <span class="text-sm text-gray-500">Da</span>
                                    <p class="text-2xl font-bold text-primary-600">
                                        €{{ number_format($catamaran->price_per_person_half_day, 0) }}
                                        <span class="text-sm font-normal text-gray-500">/persona</span>
                                    </p>
                                </div>
                                @if(($search['isAvailabilitySearch'] ?? false) && !empty($search['date']))
                                <a href="{{ route('booking.start', ['catamaran_slug' => $catamaran->slug, 'date' => $search['date']]) }}"
                                   class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                                    Prenota
                                    <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('catamarans.show', $catamaran) }}"
                                   class="inline-flex items-center px-4 py-2 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                                    Scopri
                                    <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">Nessun catamarano disponibile</h3>
                        <p class="text-gray-500">
                            @if($search['isAvailabilitySearch'] ?? false)
                                Nessuna disponibilita trovata per i criteri selezionati. Prova a cambiare data o numero ospiti.
                            @else
                                Torna a trovarci presto per scoprire la nostra flotta.
                            @endif
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="py-16 lg:py-24 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-bold text-navy-900 mb-4">Perché Scegliere Solarya Travel</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Qualità, sicurezza e comfort per un'esperienza di navigazione senza paragoni
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-navy-900 mb-2">Sicurezza Certificata</h3>
                    <p class="text-gray-600">Tutti i nostri catamarani sono regolarmente ispezionati e certificati</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-gold-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-navy-900 mb-2">Equipaggio Esperto</h3>
                    <p class="text-gray-600">Skipper professionisti con anni di esperienza nella navigazione</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-navy-900 mb-2">Prezzi Trasparenti</h3>
                    <p class="text-gray-600">Nessun costo nascosto, tutto incluso nel prezzo che vedi</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-navy-900 mb-2">Esperienza Premium</h3>
                    <p class="text-gray-600">Servizio personalizzato per rendere ogni viaggio unico</p>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-16 bg-gradient-to-r from-primary-600 to-primary-700 text-white">
        <div class="container mx-auto px-4 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold mb-4">Pronto per Salpare?</h2>
            <p class="text-xl text-primary-100 mb-8 max-w-2xl mx-auto">
                Scegli il catamarano perfetto per la tua avventura e prenota oggi stesso
            </p>
            <a href="{{ route('booking.start') }}" 
               class="inline-flex items-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-full hover:bg-primary-50 transition-colors shadow-lg">
                Prenota Ora
                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </section>
@endsection
