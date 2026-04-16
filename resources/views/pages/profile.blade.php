@extends('layouts.app')

@section('title', 'Il Mio Profilo - Solarya Travel')

@section('content')
    <section class="py-16 lg:py-24 bg-sand-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-3xl font-bold text-navy-900 mb-8">Il Mio Profilo</h1>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Sidebar --}}
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-sm p-6">
                            <div class="text-center mb-6">
                                <div class="w-24 h-24 bg-gradient-to-br from-primary-100 to-primary-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <span class="text-3xl font-bold text-primary-600">
                                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                                    </span>
                                </div>
                                <h2 class="text-xl font-semibold text-navy-900">
                                    {{ auth()->user()->name ?? 'Utente' }}
                                </h2>
                                <p class="text-gray-500 text-sm">
                                    {{ auth()->user()->email ?? 'email@esempio.it' }}
                                </p>
                            </div>

                            <nav class="space-y-2">
                                <a href="{{ route('profile') }}" 
                                   class="flex items-center px-4 py-3 bg-primary-50 text-primary-700 rounded-xl font-medium">
                                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Dati Personali
                                </a>
                                <a href="{{ route('bookings.my') }}" 
                                   class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl transition-colors">
                                    <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    Le Mie Prenotazioni
                                </a>
                            </nav>
                        </div>
                    </div>

                    {{-- Main Content --}}
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-sm p-8">
                            <h3 class="text-xl font-bold text-navy-900 mb-6">Dati Personali</h3>
                            
                            @if(session('success'))
                                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                                @csrf
                                @method('PUT')
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                            Nome Completo
                                        </label>
                                        <input 
                                            type="text" 
                                            id="name" 
                                            name="name" 
                                            value="{{ old('name', auth()->user()->name ?? '') }}"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('name') border-red-500 @enderror"
                                        >
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                            Email
                                        </label>
                                        <input 
                                            type="email" 
                                            id="email" 
                                            name="email" 
                                            value="{{ old('email', auth()->user()->email ?? '') }}"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('email') border-red-500 @enderror"
                                        >
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                            Telefono
                                        </label>
                                        <input 
                                            type="tel" 
                                            id="phone" 
                                            name="phone" 
                                            value="{{ old('phone', auth()->user()->phone ?? '') }}"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('phone') border-red-500 @enderror"
                                        >
                                        @error('phone')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                                            Data di Nascita
                                        </label>
                                        <input 
                                            type="date" 
                                            id="date_of_birth" 
                                            name="date_of_birth" 
                                            value="{{ old('date_of_birth', auth()->user()->date_of_birth ?? '') }}"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('date_of_birth') border-red-500 @enderror"
                                        >
                                        @error('date_of_birth')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="pt-4">
                                    <button type="submit" 
                                            class="px-6 py-3 bg-primary-600 text-white font-semibold rounded-xl hover:bg-primary-700 transition-colors">
                                        Salva Modifiche
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Change Password --}}
                        <div class="bg-white rounded-2xl shadow-sm p-8 mt-8">
                            <h3 class="text-xl font-bold text-navy-900 mb-6">Cambia Password</h3>
                            
                            <form action="{{ route('profile.password') }}" method="POST" class="space-y-6">
                                @csrf
                                @method('PUT')
                                
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Password Attuale
                                    </label>
                                    <input 
                                        type="password" 
                                        id="current_password" 
                                        name="current_password"
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('current_password') border-red-500 @enderror"
                                    >
                                    @error('current_password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                            Nuova Password
                                        </label>
                                        <input 
                                            type="password" 
                                            id="password" 
                                            name="password"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('password') border-red-500 @enderror"
                                        >
                                        @error('password')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                            Conferma Password
                                        </label>
                                        <input 
                                            type="password" 
                                            id="password_confirmation" 
                                            name="password_confirmation"
                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                        >
                                    </div>
                                </div>

                                <div class="pt-4">
                                    <button type="submit" 
                                            class="px-6 py-3 bg-navy-600 text-white font-semibold rounded-xl hover:bg-navy-700 transition-colors">
                                        Aggiorna Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
