<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Solarya Travel') – Escursioni in Catamarano</title>
    <meta name="description" content="@yield('meta_description', 'Vivi esperienze esclusive in catamarano lungo la Costiera. Solarya Travel: lusso, comfort ed eleganza in mare.')">

    <link rel="icon" type="image/png" href="{{ asset('images/logo_black.svg') }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100..900;1,100..900&family=Outfit:wght@100..900&display=swap">

    {{-- Template CSS --}}
    <link rel="stylesheet" href="{{ asset('assets/template/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/css/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/css/flatpicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/css/fontawesome-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/css/odometer.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/css/default.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/css/main.css') }}">

    {{-- Override font segoepr/chillax → Frezbie --}}
    <style>
        @font-face {
            font-family: 'Frezbie';
            src: url('{{ asset('fonts/Frezbie.woff2') }}') format('woff2'),
                 url('{{ asset('fonts/Frezbie.woff') }}') format('woff'),
                 url('{{ asset('fonts/Frezbie.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }
        :root {
            --tg-ff-segoepr: 'Frezbie', cursive !important;
            --tg-ff-chillax: 'Frezbie', sans-serif !important;
        }
        /* Forza Frezbie ovunque venga usato segoepr o chillax e rimuove bold/italic/corsivo */
        .tg-section-subtitle,
        [class*="tg-section-subtitle"],
        .tg-hero-subtitle,
        .tg-banner-subtitle,
        .tg-cta-subtitle,
        .tg-about-subtitle,
        .tg-chose-big-text h2,
        .tg-banner-2-big-title h2,
        [style*="--tg-ff-segoepr"],
        [style*="--tg-ff-chillax"] {
            font-family: 'Frezbie', cursive !important;
            font-weight: normal !important;
            font-style: normal !important;
            font-variant: normal !important;
            text-transform: none !important;
        }
    </style>

    @livewireStyles
    @stack('head')
</head>
<body>
    @include('partials.public.header')

    <main>
        @yield('content')
    </main>

    @include('partials.public.footer')

    {{-- Scroll to top --}}
    <button id="scrollUp" title="Scroll To Top" aria-label="Scroll to top">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/wow.js@1.2.2/dist/wow.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4/dist/flatpickr.min.js"></script>

    <script>
        // WOW
        if (typeof WOW !== 'undefined') {
            new WOW({ live: false }).init();
        }
        // Scroll-up button
        const scrollUp = document.getElementById('scrollUp');
        if (scrollUp) {
            window.addEventListener('scroll', () => {
                scrollUp.classList.toggle('show', window.scrollY > 400);
            });
            scrollUp.addEventListener('click', (e) => {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }
        // Sticky header
        const header = document.getElementById('header-sticky');
        if (header) {
            window.addEventListener('scroll', () => {
                header.classList.toggle('header-sticky', window.scrollY > 80);
            });
        }
    </script>

    <style>
        #scrollUp {
            position: fixed; right: 24px; bottom: 24px; width: 44px; height: 44px;
            border-radius: 50%; border: 0; background: #7C37FF; color: #fff;
            display: none; align-items: center; justify-content: center;
            box-shadow: 0 6px 20px rgba(124,55,255,.35); cursor: pointer; z-index: 1040;
            transition: all .25s ease;
        }
        #scrollUp.show { display: inline-flex; }
        #scrollUp:hover { transform: translateY(-3px); }

        /* Header: container-fluid limitato a 1860px su desktop large */
        @media (min-width: 1400px) {
            #header-sticky > .container-fluid {
                max-width: 1860px;
                margin-left: auto;
                margin-right: auto;
            }
        }
    </style>

    @livewireScripts
    @stack('scripts')
</body>
</html>
