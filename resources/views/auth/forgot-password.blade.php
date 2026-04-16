@extends('layouts.guest')

@section('title', 'Password Dimenticata')

@section('content')
    <div class="text-center mb-8">
        <h1 class="text-3xl font-serif font-bold text-navy-900 mb-2">Password Dimenticata?</h1>
        <p class="text-gray-600">
            Nessun problema! Inserisci la tua email e ti invieremo un link per reimpostare la password.
        </p>
    </div>

    @if (session('status'))
        <x-alert type="success" class="mb-6">
            {{ session('status') }}
        </x-alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <x-input
            type="email"
            name="email"
            label="Email"
            :value="old('email')"
            required
            autofocus
            placeholder="mario.rossi@email.com"
        />

        <x-button type="primary" class="w-full">
            Invia Link di Reset
        </x-button>
    </form>

    <div class="mt-8 text-center">
        <a href="{{ route('login') }}" class="text-primary-600 font-medium hover:text-primary-700 inline-flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Torna al login
        </a>
    </div>
@endsection
