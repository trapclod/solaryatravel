@extends('layouts.guest')

@section('title', 'Accedi')

@section('content')
    <div class="text-center mb-8">
        <h1 class="text-3xl font-serif font-bold text-navy-900 mb-2">Bentornato</h1>
        <p class="text-gray-600">Accedi al tuo account Solarya Travel</p>
    </div>

    @if (session('status'))
        <x-alert type="success" class="mb-6">
            {{ session('status') }}
        </x-alert>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <x-input
            type="email"
            name="email"
            label="Email"
            :value="old('email')"
            required
            autofocus
            autocomplete="username"
            placeholder="mario.rossi@email.com"
        />

        <x-input
            type="password"
            name="password"
            label="Password"
            required
            autocomplete="current-password"
        />

        <div class="flex items-center justify-between">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" name="remember" 
                       class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                <span class="ml-2 text-sm text-gray-600">Ricordami</span>
            </label>

            <a href="{{ route('password.request') }}" class="text-sm text-primary-600 hover:text-primary-700">
                Password dimenticata?
            </a>
        </div>

        <x-button type="primary" class="w-full">
            Accedi
        </x-button>
    </form>

    <div class="mt-8 text-center">
        <p class="text-gray-600">
            Non hai un account? 
            <a href="{{ route('register') }}" class="text-primary-600 font-medium hover:text-primary-700">
                Registrati
            </a>
        </p>
    </div>

    <!-- Social Login -->
    <div class="mt-8">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-4 bg-white text-gray-500">oppure</span>
            </div>
        </div>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                Puoi prenotare anche senza registrazione
            </p>
            <a href="{{ route('booking.start') }}" class="inline-flex items-center mt-2 text-primary-600 font-medium hover:text-primary-700">
                Prenota come ospite
                <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </div>
@endsection
