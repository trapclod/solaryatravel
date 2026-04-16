@extends('layouts.guest')

@section('title', 'Reimposta Password')

@section('content')
    <div class="text-center mb-8">
        <h1 class="text-3xl font-serif font-bold text-navy-900 mb-2">Reimposta la Password</h1>
        <p class="text-gray-600">Inserisci la tua nuova password</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-input
            type="email"
            name="email"
            label="Email"
            :value="old('email', $request->email)"
            required
            autofocus
            autocomplete="username"
        />

        <x-input
            type="password"
            name="password"
            label="Nuova Password"
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

        <x-button type="primary" class="w-full">
            Reimposta Password
        </x-button>
    </form>
@endsection
