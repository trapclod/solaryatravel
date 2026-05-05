@extends('layouts.public')

@section('title', 'I Nostri Catamarani')

@php
    use Illuminate\Support\Str;

    $featuresList = collect($availableFeatures ?? [])->values();
    $selectedFeatures = (array) ($search['features'] ?? []);
    $selectedCapacity = $search['capacity_filter'] ?? 'all';
    $selectedSort = $search['sort'] ?? 'default';
@endphp

@section('content')
    {{-- Breadcrumb --}}
    <div class="tg-breadcrumb-area tg-breadcrumb-spacing fix p-relative z-index-1 include-bg"
         style="background-image: url('{{ asset('assets/template/img/breadcrumb/breadcrumb.jpg') }}');">
        <div class="tg-hero-top-shadow"></div>
        <div class="tg-breadcrumb-shadow"></div>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="tg-breadcrumb-content text-center">
                        <h2 class="tg-breadcrumb-title mb-15">Esplora la Nostra Flotta</h2>
                        <div class="tg-breadcrumb-list">
                            <span><a href="{{ route('home') }}">Home</a></span>
                            <span class="dvdr"><i class="fa-sharp fa-solid fa-angle-right"></i></span>
                            <span>Catamarani</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tg-hero-bottom-shape d-none d-md-block">
            <span>
                <svg width="432" height="290" viewBox="0 0 432 290" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path class="line-1" opacity="0.4" d="M39.6042 428.345C4.41235 355.065 -24.3018 203.867 142.377 185.309C350.725 162.111 488.893 393.541 289.169 313.515C129.389 249.494 458.202 85.4772 642.58 11.4713" stroke="white" stroke-width="24" />
                </svg>
            </span>
        </div>
        <div class="tg-hero-bottom-shape-2 d-none d-md-block">
            <span>
                <svg width="154" height="243" viewBox="0 0 154 243" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path class="line-1" opacity="0.4" d="M144.616 328.905C116.117 300.508 62.5986 230.961 76.5162 179.949C93.9131 116.184 275.231 7.44494 -65.0181 12.8762" stroke="white" stroke-width="24" />
                </svg>
            </span>
        </div>
    </div>

    <form id="catamaransFilterForm" method="GET" action="{{ route('catamarans.index') }}">

        {{-- Booking form --}}
        <div class="tg-booking-form-area tg-booking-form-grid-space pb-50">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="tg-booking-form-item tg-booking-form-grid">
                            <div class="tg-booking-form-input-group d-flex align-items-end justify-content-between flex-wrap">

                                <div class="tg-booking-form-parent-inner mr-15 mb-15">
                                    <span class="tg-booking-form-title mb-5">Data:</span>
                                    <div class="tg-booking-add-input-date p-relative">
                                        <span>
                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.76501 0.777771V3.26668M4.23413 0.777771V3.26668M0.777344 5.75548H13.2218M2.16006 2.02211H11.8391C12.6027 2.02211 13.2218 2.57927 13.2218 3.26656V11.9778C13.2218 12.6651 12.6027 13.2222 11.8391 13.2222H2.16006C1.39641 13.2222 0.777344 12.6651 0.777344 11.9778V3.26656C0.777344 2.57927 1.39641 2.02211 2.16006 2.02211Z" stroke="currentColor" stroke-width="0.977778" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <input class="input bf-flatpickr" type="text" name="date"
                                               value="{{ $search['date'] ?? '' }}"
                                               placeholder="gg/mm/aaaa"
                                               data-min="{{ now()->addHours(config('booking.advance_hours', 24))->toDateString() }}">
                                    </div>
                                </div>

                                <div class="tg-booking-form-parent-inner mr-15 mb-15">
                                    <span class="tg-booking-form-title mb-5">Durata:</span>
                                    <div class="tg-booking-add-input-field">
                                        <select name="slot_type" class="bf-native-select">
                                            <option value="">Tutte</option>
                                            <option value="half_day" @selected(($search['slot_type'] ?? '') === 'half_day')>Mezza giornata</option>
                                            <option value="full_day" @selected(($search['slot_type'] ?? '') === 'full_day')>Giornata intera</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="tg-booking-form-parent-inner tg-hero-quantity p-relative mr-15 mb-15" id="bfGuestRoot">
                                    <span class="tg-booking-form-title mb-5">Ospiti:</span>
                                    <div id="bfGuestToggle" class="tg-booking-add-input-field tg-booking-quantity-toggle">
                                        <span class="location">
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M8 8c1.66 0 3-1.34 3-3S9.66 2 8 2 5 3.34 5 5s1.34 3 3 3zm0 1.5c-2 0-6 1-6 3V14h12v-1.5c0-2-4-3-6-3z" fill="currentColor"/>
                                            </svg>
                                        </span>
                                        <span class="tg-booking-title-value" id="bfGuestLabel">{{ ($search['adults'] ?? 2) }} adulti, {{ ($search['children'] ?? 0) }} bambini</span>
                                    </div>
                                    <input type="hidden" name="adults" id="bfAdults" value="{{ $search['adults'] ?? 2 }}">
                                    <input type="hidden" name="children" id="bfChildren" value="{{ $search['children'] ?? 0 }}">

                                    <div id="bfGuestPanel" class="tg-booking-form-location-list tg-quantity tg-booking-quantity-active">
                                        <ul>
                                            <li>
                                                <span class="mr-20">Adulti</span>
                                                <div class="tg-booking-quantity-item">
                                                    <span class="decrement" data-target="bfAdults" data-min="1">
                                                        <svg width="14" height="2" viewBox="0 0 14 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M1 1H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                        </svg>
                                                    </span>
                                                    <input class="tg-quantity-input" type="text" data-display="bfAdults" value="{{ $search['adults'] ?? 2 }}" readonly>
                                                    <span class="increment" data-target="bfAdults" data-max="20">
                                                        <svg width="15" height="14" viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M1.22 7h12.16M7.3 13V1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </li>
                                            <li>
                                                <span class="mr-20">Bambini</span>
                                                <div class="tg-booking-quantity-item">
                                                    <span class="decrement" data-target="bfChildren" data-min="0">
                                                        <svg width="14" height="2" viewBox="0 0 14 2" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M1 1H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                        </svg>
                                                    </span>
                                                    <input class="tg-quantity-input" type="text" data-display="bfChildren" value="{{ $search['children'] ?? 0 }}" readonly>
                                                    <span class="increment" data-target="bfChildren" data-max="20">
                                                        <svg width="15" height="14" viewBox="0 0 15 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M1.22 7h12.16M7.3 13V1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </li>
                                        </ul>
                                        <div class="tg-booking-form-search-btn mt-15">
                                            <button type="button" id="bfGuestOk" class="bk-search-button bk-search-button-2 w-100">Ok</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="tg-booking-form-search-btn mb-15">
                                    <button type="submit" class="bk-search-button">Cerca</button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Listing --}}
        <div class="tg-listing-grid-area mb-85">
            <div class="container">
                <div class="row">

                    {{-- Sidebar --}}
                    <div class="col-xl-3 col-lg-4 order-last order-lg-first">
                        <div class="tg-filter-sidebar mb-40 top-sticky">
                            <div class="tg-filter-item">

                                <h4 class="tg-filter-title mb-15">Capacità</h4>
                                <div class="tg-filter-list">
                                    <ul>
                                        @foreach ([['k' => 'all', 'l' => 'Tutte'], ['k' => 'small', 'l' => 'Fino a 8 ospiti'], ['k' => 'medium', 'l' => 'Da 9 a 14 ospiti'], ['k' => 'large', 'l' => 'Oltre 14 ospiti']] as $cap)
                                            <li>
                                                <div class="checkbox d-flex">
                                                    <input class="tg-checkbox auto-submit" type="radio" name="capacity_filter" id="cap_{{ $cap['k'] }}" value="{{ $cap['k'] }}" @checked($selectedCapacity === $cap['k'])>
                                                    <label for="cap_{{ $cap['k'] }}" class="tg-label">{{ $cap['l'] }}</label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                @if ($featuresList->isNotEmpty())
                                    <span class="tg-filter-border mt-25 mb-25"></span>
                                    <h4 class="tg-filter-title mb-15">Caratteristiche</h4>
                                    <div class="tg-filter-list">
                                        <ul>
                                            @foreach ($featuresList as $i => $feat)
                                                <li>
                                                    <div class="checkbox d-flex">
                                                        <input class="tg-checkbox auto-submit" type="checkbox" name="features[]" id="feat_{{ $i }}" value="{{ $feat }}" @checked(in_array($feat, $selectedFeatures, true))>
                                                        <label for="feat_{{ $i }}" class="tg-label">{{ $feat }}</label>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if (count($selectedFeatures) || $selectedCapacity !== 'all' || $selectedSort !== 'default')
                                    <span class="tg-filter-border mt-25 mb-25"></span>
                                    <a href="{{ route('catamarans.index') }}" class="tg-btn w-100 text-center">Reset filtri</a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Grid --}}
                    <div class="col-xl-9 col-lg-8">
                        <div class="tg-listing-item-box-wrap ml-10">

                            <div class="tg-listing-box-filter mb-15">
                                <div class="row align-items-center">
                                    <div class="col-lg-6 col-md-6 mb-15">
                                        <div class="tg-listing-box-number-found">
                                            <span>
                                                @if ($catamarans->count())
                                                    Trovati <strong>{{ $catamarans->count() }}</strong> {{ Str::plural('catamarano', $catamarans->count()) }}
                                                @else
                                                    Nessun catamarano trovato
                                                @endif
                                                @if (!empty($search['date']))
                                                    il {{ \Carbon\Carbon::parse($search['date'])->locale('it')->isoFormat('D MMMM YYYY') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 mb-15">
                                        <div class="tg-listing-box-view-type d-flex justify-content-end align-items-center">
                                            <div class="tg-listing-sort">
                                                <span>Ordina:</span>
                                            </div>
                                            <div class="tg-listing-select-price ml-10">
                                                <select class="bf-native-select auto-submit" name="sort">
                                                    <option value="default" @selected($selectedSort === 'default')>Predefinito</option>
                                                    <option value="price_asc" @selected($selectedSort === 'price_asc')>Prezzo crescente</option>
                                                    <option value="price_desc" @selected($selectedSort === 'price_desc')>Prezzo decrescente</option>
                                                    <option value="capacity_desc" @selected($selectedSort === 'capacity_desc')>Più capienti</option>
                                                    <option value="capacity_asc" @selected($selectedSort === 'capacity_asc')>Meno capienti</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tg-listing-grid-item">
                                <div class="row list-card list-card-open" id="catamaransGrid">
                                    @forelse ($catamarans as $i => $catamaran)
                                        @php
                                            $primary = $catamaran->primaryImage;
                                            $imgUrl = $primary
                                                ? asset('storage/' . $primary->image_path)
                                                : asset('assets/template/img/hero/hero-' . ((($i % 5) + 1)) . '.jpg');
                                            $imgAlt = ($primary?->image_alt) ?: $catamaran->name;
                                            $price = $catamaran->price_per_person_half_day ?? $catamaran->base_price_half_day;
                                            $isAvailSearch = $search['isAvailabilitySearch'] ?? false;
                                            $detailUrl = route('catamarans.show', $catamaran->slug);
                                        @endphp
                                        <div class="col-12 tg-grid-full">
                                            <div class="tg-listing-card-item tg-listing-card-clickable mb-30 p-relative">
                                                <a class="tg-listing-card-overlay" href="{{ $detailUrl }}" aria-label="{{ $catamaran->name }}"></a>
                                                <div class="tg-listing-card-thumb fix mb-15 p-relative">
                                                    <img class="tg-card-border w-100" src="{{ $imgUrl }}" alt="{{ $imgAlt }}">
                                                    @if ($isAvailSearch && isset($catamaran->matched_seats_available))
                                                        <span class="tg-listing-item-price-discount shape">{{ $catamaran->matched_seats_available }} {{ Str::plural('posto', $catamaran->matched_seats_available) }}</span>
                                                    @elseif ($catamaran->capacity)
                                                        <span class="tg-listing-item-price-discount shape">Max {{ $catamaran->capacity }}</span>
                                                    @endif
                                                </div>
                                                <div class="tg-listing-main-content">
                                                    <div class="tg-listing-card-content">
                                                        <h4 class="tg-listing-card-title">
                                                            <a href="{{ $detailUrl }}">{{ $catamaran->name }}</a>
                                                        </h4>
                                                        @if ($catamaran->description_short)
                                                            <p class="tg-listing-card-text mb-10">{{ Str::limit($catamaran->description_short, 160) }}</p>
                                                        @endif
                                                        <div class="tg-listing-card-duration-tour">
                                                            @if ($catamaran->capacity)
                                                                <span class="tg-listing-card-duration-map mb-5">
                                                                    <i class="fa-regular fa-user me-1"></i>
                                                                    Fino a {{ $catamaran->capacity }} ospiti
                                                                </span>
                                                            @endif
                                                            @if ($catamaran->length_meters)
                                                                <span class="tg-listing-card-duration-time">
                                                                    <i class="fa-regular fa-ruler-horizontal me-1"></i>
                                                                    {{ rtrim(rtrim(number_format((float) $catamaran->length_meters, 2, ',', '.'), '0'), ',') }} m
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="tg-listing-card-price d-flex align-items-end justify-content-between">
                                                        <div class="tg-listing-card-price-wrap price-bg d-flex align-items-center">
                                                            <span class="tg-listing-card-currency-amount mr-5">
                                                                <span class="currency-symbol">€</span>{{ number_format((float) ($price ?? 0), 0, ',', '.') }}
                                                            </span>
                                                            <span class="tg-listing-card-activity-person">/persona</span>
                                                        </div>
                                                        <span class="tg-btn tg-listing-card-cta">Scopri di più</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center py-5">
                                            <h3 class="h4 mb-2">Nessun catamarano disponibile</h3>
                                            <p class="text-muted mb-0">
                                                @if ($search['isAvailabilitySearch'] ?? false)
                                                    Nessuna disponibilità per i criteri selezionati. Prova a cambiare data o numero di ospiti.
                                                @else
                                                    Prova a modificare i filtri o torna a trovarci presto.
                                                @endif
                                            </p>
                                            @if (count($selectedFeatures) || $selectedCapacity !== 'all')
                                                <a href="{{ route('catamarans.index') }}" class="tg-btn mt-15">Rimuovi filtri</a>
                                            @endif
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
    if (typeof flatpickr !== 'undefined') {
        document.querySelectorAll('.bf-flatpickr').forEach(function (el) {
            flatpickr(el, {
                dateFormat: 'd/m/Y',
                minDate: el.dataset.min || 'today',
                disableMobile: true,
            });
        });
    }

    (function () {
        var root = document.getElementById('bfGuestRoot');
        if (!root) return;
        var toggle = document.getElementById('bfGuestToggle');
        var panel = document.getElementById('bfGuestPanel');
        var label = document.getElementById('bfGuestLabel');
        var adultsInput = document.getElementById('bfAdults');
        var childrenInput = document.getElementById('bfChildren');
        var okBtn = document.getElementById('bfGuestOk');

        function refreshLabel() {
            label.textContent = adultsInput.value + ' adulti, ' + childrenInput.value + ' bambini';
        }
        function open() { panel.classList.add('tg-list-open'); toggle.classList.add('active'); }
        function close() { panel.classList.remove('tg-list-open'); toggle.classList.remove('active'); }

        toggle.addEventListener('click', function (e) { e.stopPropagation(); panel.classList.contains('tg-list-open') ? close() : open(); });
        panel.addEventListener('click', function (e) { e.stopPropagation(); });
        document.addEventListener('click', function (e) { if (!root.contains(e.target)) close(); });
        if (okBtn) okBtn.addEventListener('click', close);

        root.querySelectorAll('.increment, .decrement').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var targetId = btn.dataset.target;
                var hidden = document.getElementById(targetId);
                var display = root.querySelector('input[data-display="' + targetId + '"]');
                var val = parseInt(hidden.value || '0', 10);
                if (btn.classList.contains('increment')) {
                    var max = parseInt(btn.dataset.max || '99', 10);
                    if (val < max) val++;
                } else {
                    var min = parseInt(btn.dataset.min || '0', 10);
                    if (val > min) val--;
                }
                hidden.value = val;
                if (display) display.value = val;
                refreshLabel();
            });
        });
        refreshLabel();
    })();

    (function () {
        var form = document.getElementById('catamaransFilterForm');
        if (!form) return;
        form.addEventListener('change', function (e) {
            var t = e.target;
            if (t && t.classList && t.classList.contains('auto-submit')) {
                form.submit();
            }
        });
    })();
</script>
@endpush

@push('head')
<style>
    .tg-listing-card-clickable { cursor: pointer; transition: transform .25s ease, box-shadow .25s ease; }
    .tg-listing-card-clickable:hover { transform: translateY(-3px); box-shadow: 0 18px 40px rgba(0,0,0,.08); }
    .tg-listing-card-overlay {
        position: absolute; inset: 0; z-index: 1;
        text-indent: -9999px; overflow: hidden;
    }
    .tg-listing-card-clickable a:not(.tg-listing-card-overlay),
    .tg-listing-card-clickable .tg-listing-card-cta { position: relative; z-index: 2; }
    .tg-listing-card-cta { display: inline-block; padding: 8px 22px; }

    /* Force list layout: 1 card per row, image on the left */
    @media (min-width: 768px) {
        #catamaransGrid .tg-listing-card-item { display: grid; grid-template-columns: 360px 1fr; gap: 24px; align-items: stretch; }
        #catamaransGrid .tg-listing-card-thumb { margin-bottom: 0 !important; height: 100%; }
        #catamaransGrid .tg-listing-card-thumb img { height: 100%; object-fit: cover; }
        #catamaransGrid .tg-listing-main-content { padding: 18px 22px 18px 0; display: flex; flex-direction: column; justify-content: space-between; }
    }
</style>
@endpush
