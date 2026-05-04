@extends('layouts.guest')

@section('title', 'Password Dimenticata')

@section('content')
    <div class="text-center mb-4">
        <h1 class="font-serif fw-bold text-navy h3 mb-2">Password Dimenticata?</h1>
        <p class="text-muted">Nessun problema! Inserisci la tua email e ti invieremo un link per reimpostare la password.</p>
    </div>

    @if (session('status'))
        <x-alert type="success" class="mb-4">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <x-input type="email" name="email" label="Email" :value="old('email')" required autofocus placeholder="mario.rossi@email.com" />
        <x-button type="primary" class="w-100">Invia Link di Reset</x-button>
    </form>

    <div class="mt-4 text-center">
        <a href="{{ route('login') }}" class="text-primary fw-medium text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>Torna al login
        </a>
    </div>
@endsection
