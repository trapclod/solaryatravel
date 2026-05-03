<div class="min-h-screen bg-gradient-to-b from-sand-50 to-white py-8 lg:py-12">
    <div class="container mx-auto px-4 lg:px-8 max-w-6xl">
        
        <!-- Progress Steps -->
        <div class="mb-8 lg:mb-12">
            <div class="flex items-center justify-center">
                @foreach(['Catamarano', 'Data & Ora', 'Ospiti', 'Extra', 'Conferma'] as $index => $stepName)
                    <div class="flex items-center">
                        <button 
                            wire:click="goToStep({{ $index + 1 }})"
                            @class([
                                'flex items-center justify-center w-10 h-10 rounded-full font-semibold text-sm transition-all duration-200',
                                'bg-gold-500 text-white shadow-lg' => $step === $index + 1,
                                'bg-green-500 text-white cursor-pointer hover:bg-green-600' => $step > $index + 1,
                                'bg-gray-200 text-gray-500' => $step < $index + 1,
                            ])
                            @disabled($step < $index + 1)
                        >
                            @if($step > $index + 1)
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </button>
                        <span @class([
                            'hidden sm:block ml-2 text-sm font-medium',
                            'text-gold-600' => $step === $index + 1,
                            'text-green-600' => $step > $index + 1,
                            'text-gray-400' => $step < $index + 1,
                        ])>{{ $stepName }}</span>
                        
                        @if($index < 4)
                            <div @class([
                                'w-8 lg:w-16 h-0.5 mx-2 lg:mx-4',
                                'bg-green-500' => $step > $index + 1,
                                'bg-gray-200' => $step <= $index + 1,
                            ])></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <x-card>
                    {{-- Step 1: Catamaran Selection --}}
                    @if($step === 1)
                        <div>
                            <h2 class="text-2xl font-serif font-semibold text-navy-900 mb-2">
                                Scegli il tuo catamarano
                            </h2>
                            <p class="text-gray-600 mb-8">
                                Seleziona l'imbarcazione perfetta per la tua esperienza in mare.
                            </p>

                            <div class="space-y-6">
                                @foreach($catamarans as $catamaran)
                                    <div 
                                        wire:click="selectCatamaran({{ $catamaran->id }})"
                                        @class([
                                            'relative border-2 rounded-2xl p-4 cursor-pointer transition-all duration-200 hover:shadow-lg',
                                            'border-gold-500 bg-gold-50 ring-2 ring-gold-500' => $selectedCatamaran?->id === $catamaran->id,
                                            'border-gray-200 hover:border-gold-300' => $selectedCatamaran?->id !== $catamaran->id,
                                        ])
                                    >
                                        <div class="flex flex-col sm:flex-row gap-4">
                                            {{-- Image --}}
                                            <div class="w-full sm:w-48 h-32 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                                @if($catamaran->primaryImage)
                                                    <img src="{{ $catamaran->primaryImage->url }}" 
                                                         alt="{{ $catamaran->name }}"
                                                         class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                        <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            {{-- Info --}}
                                            <div class="flex-1">
                                                <h3 class="text-xl font-semibold text-navy-900">{{ $catamaran->name }}</h3>
                                                <p class="text-gray-600 text-sm mt-1">{{ $catamaran->description_short }}</p>
                                                
                                                <div class="flex flex-wrap gap-4 mt-3 text-sm text-gray-500">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                        </svg>
                                                        {{ $catamaran->capacity }} ospiti
                                                    </span>
                                                    @if($catamaran->length_meters)
                                                        <span class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                                            </svg>
                                                            {{ $catamaran->length_meters }}m
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="mt-4 flex items-baseline gap-2">
                                                    <span class="text-2xl font-bold text-primary-600">
                                                        €{{ number_format($catamaran->price_per_person_half_day, 0) }}
                                                    </span>
                                                    <span class="text-gray-500">/ persona (mezza giornata)</span>
                                                </div>
                                            </div>
                                        </div>

                                        @if($selectedCatamaran?->id === $catamaran->id)
                                            <div class="absolute top-3 right-3 bg-gold-500 text-white rounded-full p-1">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Step 2: Date & Time Selection --}}
                    @if($step === 2)
                        <div>
                            <h2 class="text-2xl font-serif font-semibold text-navy-900 mb-2">
                                Seleziona data e orario
                            </h2>
                            <p class="text-gray-600 mb-8">
                                Scegli quando vuoi vivere la tua esperienza su {{ $selectedCatamaran->name }}.
                            </p>

                            @if($prefillDateError)
                                <x-alert type="warning" class="mb-6">
                                    {{ $prefillDateError }}
                                </x-alert>
                            @endif

                            {{-- Calendar --}}
                            <div class="mb-8">
                                <livewire:public.booking-calendar 
                                    :catamaran-id="$selectedCatamaran->id"
                                    :available-dates="$availableDates"
                                    :selected-date="$date"
                                />
                            </div>

                            {{-- Time Slots --}}
                            @if($date)
                                <div>
                                    <h3 class="text-lg font-semibold text-navy-900 mb-4">
                                        Orari disponibili per {{ \Carbon\Carbon::parse($date)->locale('it')->isoFormat('dddd D MMMM') }}
                                    </h3>

                                    @if(count($availableSlots) > 0)
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            @foreach($availableSlots as $slotData)
                                                <div 
                                                    wire:click="selectTimeSlot({{ $slotData['slot']->id }})"
                                                    @class([
                                                        'border-2 rounded-xl p-4 cursor-pointer transition-all duration-200',
                                                        'border-gold-500 bg-gold-50' => $timeSlotId === $slotData['slot']->id,
                                                        'border-gray-200 hover:border-gold-300' => $timeSlotId !== $slotData['slot']->id,
                                                    ])
                                                >
                                                    <div class="flex justify-between items-start">
                                                        <div>
                                                            <h4 class="font-semibold text-navy-900">{{ $slotData['slot']->name }}</h4>
                                                            <p class="text-sm text-gray-500">
                                                                {{ \Carbon\Carbon::parse($slotData['slot']->start_time)->format('H:i') }} - 
                                                                {{ \Carbon\Carbon::parse($slotData['slot']->end_time)->format('H:i') }}
                                                            </p>
                                                        </div>
                                                        <span class="text-sm bg-green-100 text-green-700 px-2 py-1 rounded-full">
                                                            {{ $slotData['seats_available'] }} posti
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <x-alert type="warning">
                                            Nessun orario disponibile per questa data. Prova a selezionare un'altra data.
                                        </x-alert>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Step 3: Booking Type & Guests --}}
                    @if($step === 3)
                        <div>
                            <h2 class="text-2xl font-serif font-semibold text-navy-900 mb-2">
                                Tipo di prenotazione
                            </h2>
                            <p class="text-gray-600 mb-8">
                                Scegli come vuoi prenotare la tua esperienza.
                            </p>

                            {{-- Booking Type --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                                <div 
                                    wire:click="setBookingType('seats')"
                                    @class([
                                        'border-2 rounded-xl p-6 cursor-pointer transition-all duration-200',
                                        'border-gold-500 bg-gold-50' => $bookingType === 'seats',
                                        'border-gray-200 hover:border-gold-300' => $bookingType !== 'seats',
                                    ])
                                >
                                    <div class="flex items-center mb-3">
                                        <svg class="w-6 h-6 text-primary-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <h3 class="font-semibold text-navy-900">Prenotazione Posti</h3>
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        Prenota solo i posti che ti servono. Condividerai l'esperienza con altri ospiti.
                                    </p>
                                    <p class="mt-3 text-lg font-bold text-primary-600">
                                        Da €{{ number_format($selectedCatamaran->price_per_person_half_day, 0) }}/persona
                                    </p>
                                </div>

                                <div 
                                    wire:click="setBookingType('exclusive')"
                                    @class([
                                        'border-2 rounded-xl p-6 cursor-pointer transition-all duration-200 relative overflow-hidden',
                                        'border-gold-500 bg-gold-50' => $bookingType === 'exclusive',
                                        'border-gray-200 hover:border-gold-300' => $bookingType !== 'exclusive',
                                    ])
                                >
                                    <div class="absolute top-0 right-0 bg-gold-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg">
                                        ESCLUSIVO
                                    </div>
                                    <div class="flex items-center mb-3">
                                        <svg class="w-6 h-6 text-gold-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                        </svg>
                                        <h3 class="font-semibold text-navy-900">Escursione Privata</h3>
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        L'intero catamarano solo per te e il tuo gruppo. Massima privacy e personalizzazione.
                                    </p>
                                    <p class="mt-3 text-lg font-bold text-gold-600">
                                        €{{ number_format($selectedCatamaran->exclusive_price_half_day, 0) }}
                                    </p>
                                </div>
                            </div>

                            {{-- Number of Guests --}}
                            @if($bookingType === 'seats')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-3">
                                        Numero di ospiti
                                    </label>
                                    <div class="flex items-center space-x-4">
                                        <button 
                                            wire:click="updateSeats({{ $seats - 1 }})"
                                            class="w-12 h-12 rounded-full border-2 border-gray-300 flex items-center justify-center text-gray-600 hover:border-primary-500 hover:text-primary-600 transition-colors disabled:opacity-50"
                                            @disabled($seats <= 1)
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                            </svg>
                                        </button>
                                        <span class="text-3xl font-bold text-navy-900 w-16 text-center">{{ $seats }}</span>
                                        <button 
                                            wire:click="updateSeats({{ $seats + 1 }})"
                                            class="w-12 h-12 rounded-full border-2 border-gray-300 flex items-center justify-center text-gray-600 hover:border-primary-500 hover:text-primary-600 transition-colors disabled:opacity-50"
                                            @disabled($seats >= $availableSeats)
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-2">
                                        {{ $availableSeats }} posti disponibili per questa data
                                    </p>
                                </div>
                            @else
                                <x-alert type="info" :dismissible="false">
                                    <strong>Escursione privata</strong> - Il catamarano sarà riservato esclusivamente per il tuo gruppo fino a {{ $selectedCatamaran->capacity }} ospiti.
                                </x-alert>
                            @endif
                        </div>
                    @endif

                    {{-- Step 4: Addons --}}
                    @if($step === 4)
                        <div>
                            <h2 class="text-2xl font-serif font-semibold text-navy-900 mb-2">
                                Arricchisci la tua esperienza
                            </h2>
                            <p class="text-gray-600 mb-8">
                                Aggiungi servizi extra per rendere la tua giornata ancora più speciale.
                            </p>

                            <div class="space-y-4">
                                @foreach($addons as $addon)
                                    <div 
                                        wire:click="toggleAddon({{ $addon->id }})"
                                        @class([
                                            'border-2 rounded-xl p-4 cursor-pointer transition-all duration-200',
                                            'border-gold-500 bg-gold-50' => in_array($addon->id, $selectedAddons),
                                            'border-gray-200 hover:border-gold-300' => !in_array($addon->id, $selectedAddons),
                                        ])
                                    >
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center">
                                                    <h3 class="font-semibold text-navy-900">{{ $addon->name }}</h3>
                                                    @if($addon->requires_advance_booking)
                                                        <span class="ml-2 text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">
                                                            Prenotare {{ $addon->advance_hours }}h prima
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-600 mt-1">{{ $addon->description }}</p>
                                            </div>
                                            <div class="text-right ml-4">
                                                <p class="text-lg font-bold text-primary-600">
                                                    €{{ number_format($addon->price, 2) }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $addon->price_type === 'per_person' ? '/ persona' : ($addon->price_type === 'per_day' ? '/ giorno' : 'totale') }}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        {{-- Checkbox indicator --}}
                                        <div class="absolute top-4 right-4">
                                            <div @class([
                                                'w-6 h-6 rounded-full border-2 flex items-center justify-center',
                                                'bg-gold-500 border-gold-500 text-white' => in_array($addon->id, $selectedAddons),
                                                'border-gray-300' => !in_array($addon->id, $selectedAddons),
                                            ])>
                                                @if(in_array($addon->id, $selectedAddons))
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($addons->isEmpty())
                                <x-alert type="info" :dismissible="false">
                                    Non ci sono servizi extra disponibili al momento.
                                </x-alert>
                            @endif
                        </div>
                    @endif

                    {{-- Step 5: Customer Info & Confirmation --}}
                    @if($step === 5)
                        <div>
                            <h2 class="text-2xl font-serif font-semibold text-navy-900 mb-2">
                                I tuoi dati
                            </h2>
                            <p class="text-gray-600 mb-8">
                                Inserisci i dati per completare la prenotazione.
                            </p>

                            <form wire:submit="createBooking" class="space-y-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <x-input 
                                        name="firstName"
                                        wire:model="firstName"
                                        label="Nome"
                                        placeholder="Mario"
                                        required
                                    />
                                    <x-input 
                                        name="lastName"
                                        wire:model="lastName"
                                        label="Cognome"
                                        placeholder="Rossi"
                                        required
                                    />
                                </div>

                                <x-input 
                                    type="email"
                                    name="email"
                                    wire:model="email"
                                    label="Email"
                                    placeholder="mario.rossi@email.com"
                                    required
                                    hint="Riceverai la conferma della prenotazione a questo indirizzo"
                                />

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <x-input 
                                        type="tel"
                                        name="phone"
                                        wire:model="phone"
                                        label="Telefono"
                                        placeholder="+39 333 1234567"
                                    />
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Paese</label>
                                        <select wire:model="country" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                            <option value="IT">Italia</option>
                                            <option value="DE">Germania</option>
                                            <option value="FR">Francia</option>
                                            <option value="GB">Regno Unito</option>
                                            <option value="US">Stati Uniti</option>
                                            <option value="ES">Spagna</option>
                                            <option value="NL">Paesi Bassi</option>
                                            <option value="CH">Svizzera</option>
                                            <option value="AT">Austria</option>
                                            <option value="OTHER">Altro</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Richieste speciali (opzionale)
                                    </label>
                                    <textarea 
                                        wire:model="specialRequests"
                                        rows="3"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                        placeholder="Allergie alimentari, occasioni speciali, richieste particolari..."
                                    ></textarea>
                                </div>

                                {{-- Terms --}}
                                <div class="space-y-3 pt-4 border-t">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="checkbox" wire:model="acceptTerms" class="mt-1 w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                        <span class="ml-3 text-sm text-gray-600">
                                            Ho letto e accetto i <a href="{{ route('terms') }}" target="_blank" class="text-primary-600 hover:underline">Termini e Condizioni</a> *
                                        </span>
                                    </label>
                                    @error('acceptTerms')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                    <label class="flex items-start cursor-pointer">
                                        <input type="checkbox" wire:model="acceptPrivacy" class="mt-1 w-5 h-5 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                        <span class="ml-3 text-sm text-gray-600">
                                            Acconsento al trattamento dei miei dati secondo la <a href="{{ route('privacy') }}" target="_blank" class="text-primary-600 hover:underline">Privacy Policy</a> *
                                        </span>
                                    </label>
                                    @error('acceptPrivacy')
                                        <p class="text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- Navigation Buttons --}}
                    <div class="flex justify-between mt-8 pt-6 border-t">
                        @if($step > 1)
                            <x-button type="secondary" wire:click="previousStep">
                                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Indietro
                            </x-button>
                        @else
                            <div></div>
                        @endif

                        @if($step < $totalSteps)
                            <x-button 
                                wire:click="nextStep"
                                :disabled="!$this->validateCurrentStep()"
                            >
                                Continua
                                <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </x-button>
                        @else
                            <x-button 
                                wire:click="createBooking"
                                wire:loading.attr="disabled"
                                :disabled="$loading"
                            >
                                <span wire:loading.remove>
                                    Procedi al pagamento
                                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </span>
                                <span wire:loading>
                                    <svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Elaborazione...
                                </span>
                            </x-button>
                        @endif
                    </div>
                </x-card>
            </div>

            <!-- Sidebar: Booking Summary -->
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <x-card>
                        <h3 class="text-lg font-semibold text-navy-900 mb-4">Riepilogo prenotazione</h3>

                        @if($selectedCatamaran)
                            <div class="space-y-4">
                                {{-- Catamaran --}}
                                <div class="flex items-center pb-4 border-b">
                                    <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 mr-3">
                                        @if($selectedCatamaran->primaryImage)
                                            <img src="{{ $selectedCatamaran->primaryImage->url }}" alt="{{ $selectedCatamaran->name }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-navy-900">{{ $selectedCatamaran->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $selectedCatamaran->capacity }} ospiti max</p>
                                    </div>
                                </div>

                                {{-- Date & Time --}}
                                @if($date && $selectedTimeSlot)
                                    <div class="pb-4 border-b">
                                        <p class="text-sm text-gray-500">Data e orario</p>
                                        <p class="font-semibold text-navy-900">
                                            {{ \Carbon\Carbon::parse($date)->locale('it')->isoFormat('dddd D MMMM YYYY') }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            {{ $selectedTimeSlot->name }} ({{ \Carbon\Carbon::parse($selectedTimeSlot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($selectedTimeSlot->end_time)->format('H:i') }})
                                        </p>
                                    </div>
                                @endif

                                {{-- Guests --}}
                                @if($step >= 3)
                                    <div class="pb-4 border-b">
                                        <p class="text-sm text-gray-500">Tipo prenotazione</p>
                                        <p class="font-semibold text-navy-900">
                                            @if($bookingType === 'exclusive')
                                                Escursione Privata
                                            @else
                                                {{ $seats }} {{ $seats === 1 ? 'ospite' : 'ospiti' }}
                                            @endif
                                        </p>
                                    </div>
                                @endif

                                {{-- Pricing --}}
                                @if($step >= 3 && $basePrice > 0)
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">
                                                @if($bookingType === 'exclusive')
                                                    Escursione privata
                                                @else
                                                    {{ $seats }} x €{{ number_format($basePrice / $seats, 2) }}
                                                @endif
                                            </span>
                                            <span class="text-gray-900">€{{ number_format($basePrice, 2) }}</span>
                                        </div>

                                        @if($addonsTotal > 0)
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-600">Extra</span>
                                                <span class="text-gray-900">€{{ number_format($addonsTotal, 2) }}</span>
                                            </div>
                                        @endif

                                        @if($discountAmount > 0)
                                            <div class="flex justify-between text-sm text-green-600">
                                                <span>Sconto</span>
                                                <span>-€{{ number_format($discountAmount, 2) }}</span>
                                            </div>
                                        @endif

                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">IVA ({{ config('booking.tax_rate', 22) }}%)</span>
                                            <span class="text-gray-900">€{{ number_format($taxAmount, 2) }}</span>
                                        </div>

                                        <div class="flex justify-between pt-3 border-t mt-3">
                                            <span class="font-semibold text-navy-900">Totale</span>
                                            <span class="text-xl font-bold text-primary-600">€{{ number_format($totalAmount, 2) }}</span>
                                        </div>
                                    </div>

                                    {{-- Discount Code --}}
                                    @if($step >= 4)
                                        <div class="pt-4 border-t mt-4">
                                            @if(!$appliedDiscount)
                                                <div class="flex gap-2">
                                                    <input 
                                                        type="text" 
                                                        wire:model="discountCode"
                                                        placeholder="Codice sconto"
                                                        class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"
                                                    >
                                                    <button 
                                                        wire:click="applyDiscountCode"
                                                        class="px-4 py-2 text-sm font-medium text-primary-600 border border-primary-600 rounded-lg hover:bg-primary-50 transition-colors"
                                                    >
                                                        Applica
                                                    </button>
                                                </div>
                                                @if($discountError)
                                                    <p class="text-sm text-red-600 mt-1">{{ $discountError }}</p>
                                                @endif
                                            @else
                                                <div class="flex items-center justify-between bg-green-50 border border-green-200 rounded-lg px-3 py-2">
                                                    <div>
                                                        <p class="text-sm font-medium text-green-800">{{ $appliedDiscount->code }}</p>
                                                        <p class="text-xs text-green-600">Sconto applicato</p>
                                                    </div>
                                                    <button wire:click="removeDiscount" class="text-green-700 hover:text-green-900">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">
                                Seleziona un catamarano per vedere il riepilogo della prenotazione.
                            </p>
                        @endif
                    </x-card>

                    {{-- Trust Badges --}}
                    <div class="mt-6 space-y-3">
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Pagamento sicuro con Stripe
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Cancellazione gratuita fino a 48h prima
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Conferma immediata via email
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
