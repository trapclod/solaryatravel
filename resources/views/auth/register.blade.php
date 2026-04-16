@extends('layouts.guest')

@section('title', 'Registrati')

@section('content')
    <div class="text-center mb-8">
        <h1 class="text-3xl font-serif font-bold text-navy-900 mb-2">Crea il tuo Account</h1>
        <p class="text-gray-600">Unisciti a Solarya Travel per un'esperienza esclusiva</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-input
                type="text"
                name="first_name"
                label="Nome"
                :value="old('first_name')"
                required
                autofocus
                placeholder="Mario"
            />

            <x-input
                type="text"
                name="last_name"
                label="Cognome"
                :value="old('last_name')"
                required
                placeholder="Rossi"
            />
        </div>

        <x-input
            type="email"
            name="email"
            label="Email"
            :value="old('email')"
            required
            autocomplete="username"
            placeholder="mario.rossi@email.com"
        />

        <x-input
            type="tel"
            name="phone"
            label="Telefono (opzionale)"
            :value="old('phone')"
            placeholder="+39 333 1234567"
        />

        <x-input
            type="password"
            name="password"
            label="Password"
            required
            autocomplete="new-password"
            hint="Almeno 8 caratteri"
        />

        <x-input
            type="password"
            name="password_confirmation"
            label="Conferma Password"
            required
            autocomplete="new-password"
        />

        <div class="space-y-3 pt-4 border-t">
            <label class="flex items-start cursor-pointer">
                <input type="checkbox" name="accept_terms" value="1"
                       class="mt-1 w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                       {{ old('accept_terms') ? 'checked' : '' }}>
                <span class="ml-3 text-sm text-gray-600">
                    Ho letto e accetto i <a href="{{ route('terms') }}" target="_blank" class="text-primary-600 hover:underline">Termini e Condizioni</a> *
                </span>
            </label>
            @error('accept_terms')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror

            <label class="flex items-start cursor-pointer">
                <input type="checkbox" name="accept_privacy" value="1"
                       class="mt-1 w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                       {{ old('accept_privacy') ? 'checked' : '' }}>
                <span class="ml-3 text-sm text-gray-600">
                    Acconsento al trattamento dei miei dati secondo la <a href="{{ route('privacy') }}" target="_blank" class="text-primary-600 hover:underline">Privacy Policy</a> *
                </span>
            </label>
            @error('accept_privacy')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <x-button type="primary" class="w-full">
            Crea Account
        </x-button>
    </form>

    <div class="mt-8 text-center">
        <p class="text-gray-600">
            Hai già un account? 
            <a href="{{ route('login') }}" class="text-primary-600 font-medium hover:text-primary-700">
                Accedi
            </a>
        </p>
    </div>
@endsection
