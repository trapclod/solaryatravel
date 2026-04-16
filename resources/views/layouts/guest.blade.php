<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Solarya Travel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700|inter:300,400,500,600,700" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gradient-to-br from-primary-50 via-white to-sand-50 min-h-screen">
    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-12">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="mb-8">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-primary-600 to-primary-700 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                    </svg>
                </div>
                <div>
                    <span class="block font-serif text-2xl font-semibold text-navy-900">Solarya</span>
                    <span class="block text-xs text-gold-600 tracking-widest uppercase -mt-1">Travel</span>
                </div>
            </div>
        </a>

        <!-- Content Card -->
        <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
            @yield('content')
        </div>

        <!-- Back to Home -->
        <a href="{{ route('home') }}" class="mt-8 text-sm text-gray-500 hover:text-primary-600 transition-colors flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Torna alla Home
        </a>
    </div>

    @livewireScripts
</body>
</html>
