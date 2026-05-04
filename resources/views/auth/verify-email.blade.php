@extends('layouts.guest')

@section('title', 'Verifica Email')

@section('content')
    <div class="text-center mb-4">
        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:64px;height:64px;">
            <i class="bi bi-envelope fs-2"></i>
        </div>
        <h1 class="font-serif fw-bold text-navy h3 mb-2">Verifica la tua Email</h1>
        <p class="text-muted">Grazie per esserti registrato! Prima di iniziare, verifica il tuo indirizzo email cliccando sul link che ti abbiamo inviato.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <x-alert type="success" class="mb-4">Un nuovo link di verifica è stato inviato al tuo indirizzo email.</x-alert>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
        @csrf
        <x-button type="primary" class="w-100">Reinvia Email di Verifica</x-button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="text-center">
        @csrf
        <button type="submit" class="btn btn-link text-muted small text-decoration-none">Esci</button>
    </form>
@endsection
