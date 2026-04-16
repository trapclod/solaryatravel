@extends('layouts.app')

@section('title', $catamaran->meta_title ?: $catamaran->name . ' - Solarya Travel')
@section('meta_description', $catamaran->meta_description ?: $catamaran->description_short)

@section('content')
    {{-- Hero Section --}}
    <section class="relative bg-navy-900 text-white">
        <div class="container mx-auto px-4 lg:px-8 py-8">
            {{-- Breadcrumb --}}
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm">
                    <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors">Home</a></li>
                    <li><span class="text-gray-600">/</span></li>
                    <li><a href="{{ route('catamarans.index') }}" class="text-gray-400 hover:text-white transition-colors">Catamarani</a></li>
                    <li><span class="text-gray-600">/</span></li>
                    <li><span class="text-white">{{ $catamaran->name }}</span></li>
                </ol>
            </nav>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="py-12 lg:py-16 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                {{-- Image Gallery --}}
                <div class="space-y-4">
                    @if($catamaran->images->count() > 0)
                        {{-- Main Image --}}
                        <div class="relative aspect-[4/3] rounded-2xl overflow-hidden shadow-lg" x-data="{ activeImage: 0 }">
                            @foreach($catamaran->images as $index => $image)
                                <img src="{{ asset('storage/' . $image->image_path) }}" 
                                     alt="{{ $image->alt_text ?: $catamaran->name }}"
                                     class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                                     x-show="activeImage === {{ $index }}"
                                     x-transition:enter="ease-out duration-300"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100">
                            @endforeach

                            @if($catamaran->images->count() > 1)
                                <button @click="activeImage = (activeImage - 1 + {{ $catamaran->images->count() }}) % {{ $catamaran->images->count() }}"
                                        class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/90 rounded-full flex items-center justify-center shadow-lg hover:bg-white transition-colors">
                                    <svg class="w-5 h-5 text-navy-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button @click="activeImage = (activeImage + 1) % {{ $catamaran->images->count() }}"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/90 rounded-full flex items-center justify-center shadow-lg hover:bg-white transition-colors">
                                    <svg class="w-5 h-5 text-navy-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @endif
                        </div>

                        {{-- Thumbnails --}}
                        @if($catamaran->images->count() > 1)
                            <div class="flex space-x-3 overflow-x-auto pb-2" x-data>
                                @foreach($catamaran->images as $index => $image)
                                    <button @click="activeImage = {{ $index }}"
                                            class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-colors"
                                            :class="activeImage === {{ $index }} ? 'border-primary-500' : 'border-transparent'">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" 
                                             alt=""
                                             class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="aspect-[4/3] rounded-2xl bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center">
                            <svg class="w-24 h-24 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Details --}}
                <div>
                    <h1 class="text-3xl lg:text-4xl font-bold text-navy-900 mb-4">{{ $catamaran->name }}</h1>
                    
                    {{-- Quick Stats --}}
                    <div class="flex flex-wrap gap-4 mb-6">
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Max {{ $catamaran->capacity }} ospiti
                        </div>
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                            </svg>
                            {{ $catamaran->length_meters }} metri
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="prose prose-lg max-w-none mb-8 text-gray-600">
                        {!! nl2br(e($catamaran->description)) !!}
                    </div>

                    {{-- Features --}}
                    @php
                        $rawFeatures = $catamaran->features;
                        if (is_string($rawFeatures)) {
                            $features = json_decode($rawFeatures, true) ?? [];
                        } else {
                            $features = is_array($rawFeatures) ? $rawFeatures : [];
                        }
                    @endphp
                    @if(count($features) > 0)
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-navy-900 mb-4">Caratteristiche</h3>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($features as $feature)
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-gold-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        <span class="text-gray-700">{{ $feature }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Pricing --}}
                    <div class="bg-sand-50 rounded-2xl p-6 mb-8">
                        <h3 class="text-lg font-semibold text-navy-900 mb-4">Prezzi</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-white rounded-xl p-4 text-center">
                                <p class="text-sm text-gray-500 mb-1">Mezza Giornata</p>
                                <p class="text-3xl font-bold text-primary-600">€{{ number_format($catamaran->price_per_person_half_day, 0) }}</p>
                                <p class="text-sm text-gray-500">/persona</p>
                            </div>
                            <div class="bg-white rounded-xl p-4 text-center">
                                <p class="text-sm text-gray-500 mb-1">Giornata Intera</p>
                                <p class="text-3xl font-bold text-primary-600">€{{ number_format($catamaran->price_per_person_full_day, 0) }}</p>
                                <p class="text-sm text-gray-500">/persona</p>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-sand-200">
                            <p class="text-sm text-gray-600 text-center">
                                <strong>Escursione Privata:</strong> 
                                Da €{{ number_format($catamaran->exclusive_price_half_day, 0) }} (mezza giornata)
                            </p>
                        </div>
                    </div>

                    {{-- CTA --}}
                    <a href="{{ route('booking.catamaran', $catamaran) }}" 
                       class="w-full inline-flex items-center justify-center px-8 py-4 bg-gold-500 text-white font-semibold rounded-xl hover:bg-gold-600 transition-colors shadow-lg text-lg">
                        Prenota Questo Catamarano
                        <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Available Addons --}}
    @if($addons->count() > 0)
        <section class="py-12 bg-sand-50">
            <div class="container mx-auto px-4 lg:px-8">
                <h2 class="text-2xl font-bold text-navy-900 mb-6">Servizi Extra Disponibili</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($addons as $addon)
                        <div class="bg-white rounded-xl p-6 shadow-sm">
                            @if($addon->image_path)
                                <div class="w-12 h-12 rounded-lg overflow-hidden mb-4">
                                    <img src="{{ asset('storage/' . $addon->image_path) }}" alt="{{ $addon->name }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-12 h-12 bg-gold-100 rounded-lg flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                            @endif
                            <h3 class="font-semibold text-navy-900 mb-1">{{ $addon->name }}</h3>
                            <p class="text-sm text-gray-600 mb-3">{{ $addon->description }}</p>
                            <p class="text-primary-600 font-bold">
                                €{{ number_format($addon->price, 0) }}
                                <span class="text-sm font-normal text-gray-500">
                                    /{{ $addon->price_type === 'per_person' ? 'persona' : ($addon->price_type === 'per_booking' ? 'prenotazione' : 'unità') }}
                                </span>
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Similar Catamarans --}}
    @if($similarCatamarans->count() > 0)
        <section class="py-12 lg:py-16 bg-white">
            <div class="container mx-auto px-4 lg:px-8">
                <h2 class="text-2xl font-bold text-navy-900 mb-8">Altri Catamarani</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($similarCatamarans as $similar)
                        <article class="bg-white rounded-2xl shadow-lg overflow-hidden group hover:shadow-xl transition-shadow">
                            <a href="{{ route('catamarans.show', $similar) }}" class="block relative aspect-[4/3] overflow-hidden">
                                @if($similar->primaryImage)
                                    <img src="{{ asset('storage/' . $similar->primaryImage->image_path) }}" 
                                         alt="{{ $similar->name }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                                        </svg>
                                    </div>
                                @endif
                            </a>
                            <div class="p-5">
                                <h3 class="font-bold text-navy-900 mb-2">
                                    <a href="{{ route('catamarans.show', $similar) }}" class="hover:text-primary-600 transition-colors">
                                        {{ $similar->name }}
                                    </a>
                                </h3>
                                <div class="flex items-center justify-between">
                                    <p class="text-primary-600 font-semibold">
                                        Da €{{ number_format($similar->price_per_person_half_day, 0) }}/persona
                                    </p>
                                    <span class="text-sm text-gray-500">Max {{ $similar->capacity }} ospiti</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('gallery', () => ({
            activeImage: 0
        }))
    })
</script>
@endpush
