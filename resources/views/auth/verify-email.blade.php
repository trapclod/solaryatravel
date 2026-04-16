@extends('layouts.guest')

@section('title', 'Verifica Email')

@section('content')
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h1 class="text-3xl font-serif font-bold text-navy-900 mb-2">Verifica la tua Email</h1>
        <p class="text-gray-600">
            Grazie per esserti registrato! Prima di iniziare, verifica il tuo indirizzo email cliccando sul link che ti abbiamo inviato.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <x-alert type="success" class="mb-6">
            Un nuovo link di verifica è stato inviato al tuo indirizzo email.
        </x-alert>
    @endif

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-button type="primary" class="w-full">
                Reinvia Email di Verifica
            </x-button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button type="submit" class="text-gray-600 hover:text-gray-800 text-sm">
                Esci
            </button>
        </form>
    </div>
@endsection
