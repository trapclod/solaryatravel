@extends('layouts.app')

@section('title', 'Il Mio Profilo - Solarya Travel')

@section('content')
    <section class="py-5 bg-sand-50 min-vh-75">
        <div class="container py-4">
            <div class="mx-auto" style="max-width:960px">
                <h1 class="h2 fw-bold text-navy mb-4 font-serif">Il Mio Profilo</h1>

                <div class="row g-4">
                    {{-- Sidebar --}}
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 p-4">
                            <div class="text-center mb-4">
                                <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center bg-gradient-primary text-white fw-bold" style="width:96px;height:96px;font-size:2rem;">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                </div>
                                <h2 class="h5 fw-semibold text-navy mb-1">{{ auth()->user()->name ?? 'Utente' }}</h2>
                                <p class="text-muted small mb-0">{{ auth()->user()->email ?? 'email@esempio.it' }}</p>
                            </div>

                            <nav class="nav flex-column gap-1">
                                <a href="{{ route('profile') }}" class="nav-link rounded-3 fw-medium bg-primary-subtle text-primary">
                                    <i class="bi bi-person me-2"></i>Dati Personali
                                </a>
                                <a href="{{ route('bookings.my') }}" class="nav-link rounded-3 text-secondary">
                                    <i class="bi bi-calendar-check me-2"></i>Le Mie Prenotazioni
                                </a>
                            </nav>
                        </div>
                    </div>

                    {{-- Main --}}
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-4">
                            <h3 class="h4 fw-bold text-navy mb-4 font-serif">Dati Personali</h3>

                            @if(session('success'))
                                <x-alert type="success" class="mb-4">{{ session('success') }}</x-alert>
                            @endif

                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-input name="name" label="Nome Completo" :value="old('name', auth()->user()->name ?? '')" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-input type="email" name="email" label="Email" :value="old('email', auth()->user()->email ?? '')" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-input type="tel" name="phone" label="Telefono" :value="old('phone', auth()->user()->phone ?? '')" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-input type="date" name="date_of_birth" label="Data di Nascita" :value="old('date_of_birth', auth()->user()->date_of_birth ?? '')" />
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">Salva Modifiche</button>
                            </form>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                            <h3 class="h4 fw-bold text-navy mb-4 font-serif">Cambia Password</h3>

                            <form action="{{ route('profile.password') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <x-input type="password" name="current_password" label="Password Attuale" />
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-input type="password" name="password" label="Nuova Password" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-input type="password" name="password_confirmation" label="Conferma Password" />
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-navy rounded-pill px-4 fw-semibold">Aggiorna Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
