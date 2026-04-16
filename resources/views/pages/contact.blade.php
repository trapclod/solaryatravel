@extends('layouts.app')

@section('title', 'Contatti - Solarya Travel')

@section('content')
    {{-- Hero Section --}}
    <section class="relative bg-gradient-to-r from-navy-900 to-navy-800 text-white py-20">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="max-w-3xl">
                <h1 class="text-4xl lg:text-5xl font-bold mb-4">Contattaci</h1>
                <p class="text-xl text-gray-300">
                    Siamo qui per rispondere a tutte le tue domande. 
                    Contattaci e ti risponderemo al più presto.
                </p>
            </div>
        </div>
    </section>

    {{-- Contact Section --}}
    <section class="py-16 lg:py-24 bg-sand-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                {{-- Contact Form --}}
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-navy-900 mb-6">Inviaci un Messaggio</h2>
                    
                    @if(session('success'))
                        <x-alert type="success" class="mb-6">
                            {{ session('success') }}
                        </x-alert>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-input 
                                name="name" 
                                label="Nome Completo" 
                                required 
                                :value="old('name')"
                            />
                            <x-input 
                                type="email" 
                                name="email" 
                                label="Email" 
                                required 
                                :value="old('email')"
                            />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <x-input 
                                type="tel" 
                                name="phone" 
                                label="Telefono (opzionale)" 
                                :value="old('phone')"
                            />
                            <x-input 
                                name="subject" 
                                label="Oggetto" 
                                required 
                                :value="old('subject')"
                            />
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                Messaggio <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="message" 
                                name="message" 
                                rows="5" 
                                required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('message') border-red-500 @enderror"
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <x-button type="submit" class="w-full">
                            Invia Messaggio
                        </x-button>
                    </form>
                </div>

                {{-- Contact Info --}}
                <div class="space-y-8">
                    <div>
                        <h2 class="text-2xl font-bold text-navy-900 mb-6">Informazioni di Contatto</h2>
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-navy-900 mb-1">Indirizzo</h3>
                                    <p class="text-gray-600">
                                        Porto Turistico Marina<br>
                                        Via del Mare, 123<br>
                                        00100 Città di Mare (RM)
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-navy-900 mb-1">Telefono</h3>
                                    <p class="text-gray-600">
                                        <a href="tel:+390612345678" class="hover:text-primary-600 transition-colors">
                                            +39 06 1234 5678
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-navy-900 mb-1">Email</h3>
                                    <p class="text-gray-600">
                                        <a href="mailto:info@solaryatravel.it" class="hover:text-primary-600 transition-colors">
                                            info@solaryatravel.it
                                        </a>
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                    <svg class="w-6 h-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-navy-900 mb-1">Orari</h3>
                                    <p class="text-gray-600">
                                        Lun - Sab: 9:00 - 18:00<br>
                                        Domenica: su appuntamento
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Map placeholder --}}
                    <div class="bg-gray-200 rounded-2xl h-64 flex items-center justify-center">
                        <div class="text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <p>Mappa interattiva</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
