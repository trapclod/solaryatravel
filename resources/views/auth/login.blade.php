@extends('layouts.guest')

@section('title', 'Accedi')

@section('content')
    <div class="text-center mb-4">
        <h1 class="font-serif fw-bold text-navy h3 mb-2">Bentornato</h1>
        <p class="text-muted">Accedi al tuo account Solarya Travel</p>
    </div>

    @if (session('status'))
        <x-alert type="success" class="mb-4">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <x-input type="email" name="email" label="Email" :value="old('email')" required autofocus autocomplete="username" placeholder="mario.rossi@email.com" />
        <x-input type="password" name="password" label="Password" required autocomplete="current-password" />

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label small text-muted" for="remember">Ricordami</label>
            </div>
            <a href="{{ route('password.request') }}" class="small text-primary text-decoration-none">Password dimenticata?</a>
        </div>

        <x-button type="primary" class="w-100">Accedi</x-button>
    </form>

    <div class="mt-4 text-center text-muted">
        Non hai un account? <a href="{{ route('register') }}" class="text-primary fw-medium text-decoration-none">Registrati</a>
    </div>

    <hr class="my-4">

    <div class="text-center">
        <p class="small text-muted mb-2">Puoi prenotare anche senza registrazione</p>
        <a href="{{ route('booking.start') }}" class="text-primary fw-medium text-decoration-none">
            Prenota come ospite <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
@endsection
