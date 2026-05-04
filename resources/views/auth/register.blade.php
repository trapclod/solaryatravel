@extends('layouts.guest')

@section('title', 'Registrati')

@section('content')
    <div class="text-center mb-4">
        <h1 class="font-serif fw-bold text-navy h3 mb-2">Crea il tuo Account</h1>
        <p class="text-muted">Unisciti a Solarya Travel per un'esperienza esclusiva</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="row">
            <div class="col-sm-6">
                <x-input type="text" name="first_name" label="Nome" :value="old('first_name')" required autofocus placeholder="Mario" />
            </div>
            <div class="col-sm-6">
                <x-input type="text" name="last_name" label="Cognome" :value="old('last_name')" required placeholder="Rossi" />
            </div>
        </div>

        <x-input type="email" name="email" label="Email" :value="old('email')" required autocomplete="username" placeholder="mario.rossi@email.com" />
        <x-input type="tel" name="phone" label="Telefono (opzionale)" :value="old('phone')" placeholder="+39 333 1234567" />
        <x-input type="password" name="password" label="Password" required autocomplete="new-password" hint="Almeno 8 caratteri" />
        <x-input type="password" name="password_confirmation" label="Conferma Password" required autocomplete="new-password" />

        <hr class="my-3">

        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="accept_terms" value="1" id="accept_terms" {{ old('accept_terms') ? 'checked' : '' }}>
            <label class="form-check-label small text-muted" for="accept_terms">
                Ho letto e accetto i <a href="{{ route('terms') }}" target="_blank" class="text-primary">Termini e Condizioni</a> *
            </label>
            @error('accept_terms')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="accept_privacy" value="1" id="accept_privacy" {{ old('accept_privacy') ? 'checked' : '' }}>
            <label class="form-check-label small text-muted" for="accept_privacy">
                Acconsento al trattamento dei miei dati secondo la <a href="{{ route('privacy') }}" target="_blank" class="text-primary">Privacy Policy</a> *
            </label>
            @error('accept_privacy')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <x-button type="primary" class="w-100">Crea Account</x-button>
    </form>

    <div class="mt-4 text-center text-muted">
        Hai già un account? <a href="{{ route('login') }}" class="text-primary fw-medium text-decoration-none">Accedi</a>
    </div>
@endsection
