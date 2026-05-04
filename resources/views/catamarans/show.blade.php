@extends('layouts.app')

@section('title', $catamaran->meta_title ?: $catamaran->name . ' - Solarya Travel')
@section('meta_description', $catamaran->meta_description ?: $catamaran->description_short)

@php
    $rawFeatures = $catamaran->features;
    $features = is_string($rawFeatures) ? (json_decode($rawFeatures, true) ?? []) : (is_array($rawFeatures) ? $rawFeatures : []);
@endphp

@section('content')
    {{-- Breadcrumb --}}
    <section class="bg-navy text-white py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('catamarans.index') }}" class="text-white-50 text-decoration-none">Catamarani</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">{{ $catamaran->name }}</li>
                </ol>
            </nav>
        </div>
    </section>

    {{-- Main --}}
    <section class="py-5 bg-white">
        <div class="container py-3">
            <div class="row g-5">
                {{-- Gallery --}}
                <div class="col-lg-6">
                    @if($catamaran->images->count() > 0)
                        <div x-data="{ activeImage: 0 }">
                            <div class="ratio ratio-4x3 rounded-4 overflow-hidden shadow position-relative">
                                @foreach($catamaran->images as $index => $image)
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                         alt="{{ $image->alt_text ?: $catamaran->name }}"
                                         class="object-fit-cover w-100 h-100"
                                         x-show="activeImage === {{ $index }}"
                                         x-transition>
                                @endforeach
                                @if($catamaran->images->count() > 1)
                                    <button type="button" @click="activeImage = (activeImage - 1 + {{ $catamaran->images->count() }}) % {{ $catamaran->images->count() }}"
                                            class="btn btn-light rounded-circle shadow position-absolute top-50 start-0 translate-middle-y ms-3" style="width:40px;height:40px;">
                                        <i class="bi bi-chevron-left"></i>
                                    </button>
                                    <button type="button" @click="activeImage = (activeImage + 1) % {{ $catamaran->images->count() }}"
                                            class="btn btn-light rounded-circle shadow position-absolute top-50 end-0 translate-middle-y me-3" style="width:40px;height:40px;">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                @endif
                            </div>

                            @if($catamaran->images->count() > 1)
                                <div class="d-flex gap-2 mt-3 overflow-auto pb-2">
                                    @foreach($catamaran->images as $index => $image)
                                        <button type="button" @click="activeImage = {{ $index }}"
                                                class="border rounded-3 overflow-hidden p-0 flex-shrink-0"
                                                :class="activeImage === {{ $index }} ? 'border-primary border-2' : 'border-transparent'"
                                                style="width:80px;height:80px;">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="" class="w-100 h-100 object-fit-cover">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="ratio ratio-4x3 rounded-4 bg-gradient-primary d-flex align-items-center justify-content-center">
                            <div class="d-flex align-items-center justify-content-center"><i class="bi bi-water display-1 text-white opacity-50"></i></div>
                        </div>
                    @endif
                </div>

                {{-- Details --}}
                <div class="col-lg-6">
                    <h1 class="display-5 fw-bold text-navy mb-3 font-serif">{{ $catamaran->name }}</h1>

                    <div class="d-flex flex-wrap gap-3 text-secondary mb-4">
                        <span><i class="bi bi-people text-primary me-1"></i> Max {{ $catamaran->capacity }} ospiti</span>
                        <span><i class="bi bi-rulers text-primary me-1"></i> {{ $catamaran->length_meters }} metri</span>
                    </div>

                    <div class="text-secondary mb-4" style="white-space:pre-line;">{{ $catamaran->description }}</div>

                    @if(count($features) > 0)
                        <h3 class="h5 fw-semibold text-navy mb-3">Caratteristiche</h3>
                        <div class="row g-2 mb-4">
                            @foreach($features as $feature)
                                <div class="col-6">
                                    <i class="bi bi-check-circle-fill text-warning me-2"></i>
                                    <span class="text-secondary">{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Pricing --}}
                    <div class="bg-sand-50 rounded-4 p-4 mb-4">
                        <h3 class="h5 fw-semibold text-navy mb-3">Prezzi</h3>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="bg-white rounded-3 p-3 text-center">
                                    <small class="text-muted d-block mb-1">Mezza Giornata</small>
                                    <p class="display-6 fw-bold text-primary mb-0">€{{ number_format($catamaran->price_per_person_half_day, 0) }}</p>
                                    <small class="text-muted">/persona</small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="bg-white rounded-3 p-3 text-center">
                                    <small class="text-muted d-block mb-1">Giornata Intera</small>
                                    <p class="display-6 fw-bold text-primary mb-0">€{{ number_format($catamaran->price_per_person_full_day, 0) }}</p>
                                    <small class="text-muted">/persona</small>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <p class="small text-secondary text-center mb-0">
                            <strong>Escursione Privata:</strong>
                            Da €{{ number_format($catamaran->exclusive_price_half_day, 0) }} (mezza giornata)
                        </p>
                    </div>

                    <a href="{{ route('booking.catamaran', $catamaran) }}" class="btn btn-gold btn-lg rounded-pill w-100 shadow fw-semibold">
                        Prenota Questo Catamarano <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Addons --}}
    @if($addons->count() > 0)
        <section class="py-5 bg-sand-50">
            <div class="container py-3">
                <h2 class="h3 fw-bold text-navy mb-4 font-serif">Servizi Extra Disponibili</h2>
                <div class="row g-3">
                    @foreach($addons as $addon)
                        <div class="col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                                @if($addon->image_path)
                                    <div class="rounded-3 overflow-hidden mb-3" style="width:48px;height:48px;">
                                        <img src="{{ asset('storage/' . $addon->image_path) }}" alt="{{ $addon->name }}" class="w-100 h-100 object-fit-cover">
                                    </div>
                                @else
                                    <div class="rounded-3 bg-warning-subtle text-warning d-flex align-items-center justify-content-center mb-3" style="width:48px;height:48px;">
                                        <i class="bi bi-plus-lg fs-5"></i>
                                    </div>
                                @endif
                                <h3 class="h6 fw-semibold text-navy mb-1">{{ $addon->name }}</h3>
                                <p class="small text-secondary mb-2">{{ $addon->description }}</p>
                                <p class="fw-bold text-primary mb-0">
                                    €{{ number_format($addon->price, 0) }}
                                    <small class="fw-normal text-muted">/{{ $addon->price_type === 'per_person' ? 'persona' : ($addon->price_type === 'per_booking' ? 'prenotazione' : 'unità') }}</small>
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Similar --}}
    @if($similarCatamarans->count() > 0)
        <section class="py-5 bg-white">
            <div class="container py-3">
                <h2 class="h3 fw-bold text-navy mb-4 font-serif">Altri Catamarani</h2>
                <div class="row g-4">
                    @foreach($similarCatamarans as $similar)
                        <div class="col-md-4">
                            <article class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 card-hover">
                                <a href="{{ route('catamarans.show', $similar) }}" class="d-block ratio ratio-4x3">
                                    @if($similar->primaryImage)
                                        <img src="{{ asset('storage/' . $similar->primaryImage->image_path) }}" alt="{{ $similar->name }}" class="object-fit-cover w-100 h-100">
                                    @else
                                        <div class="bg-gradient-primary d-flex align-items-center justify-content-center"><i class="bi bi-water display-3 text-white opacity-50"></i></div>
                                    @endif
                                </a>
                                <div class="card-body p-4">
                                    <h3 class="h6 fw-bold text-navy mb-2">
                                        <a href="{{ route('catamarans.show', $similar) }}" class="text-decoration-none text-navy">{{ $similar->name }}</a>
                                    </h3>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <p class="fw-semibold text-primary mb-0">Da €{{ number_format($similar->price_per_person_half_day, 0) }}/persona</p>
                                        <small class="text-muted">Max {{ $similar->capacity }}</small>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
