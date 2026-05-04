<div class="min-vh-100 bg-gradient-to-b from-sand-50 to-white py-5 $1">
 <div class="container mx-auto px-4 $1">
 
 <!-- Progress Steps -->
 <div class="mb-5 $1">
 <div class="d-flex align-items-center justify-content-center">
 @foreach(['Catamarano', 'Data & Ora', 'Ospiti', 'Extra', 'Conferma'] as $index => $stepName)
 <div class="d-flex align-items-center">
 <button 
 wire:click="goToStep({{ $index + 1 }})"
 @class([
 'd-flex align-items-center justify-content-center w-10 h-10 rounded-full font-semibold small ',
 'bg-gold-500 text-white shadow-lg' => $step === $index + 1,
 'bg-success text-white' => $step > $index + 1,
 'bg-secondary-subtle text-muted' => $step < $index + 1,
 ])
 @disabled($step < $index + 1)
 >
 @if($step > $index + 1)
 <svg class="" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
 </svg>
 @else
 {{ $index + 1 }}
 @endif
 </button>
 <span @class([
 'd-none d-block ms-2 small fw-medium',
 'text-gold-600' => $step === $index + 1,
 'text-green-600' => $step > $index + 1,
 'text-muted' => $step < $index + 1,
 ])>{{ $stepName }}</span>
 
 @if($index < 4)
 <div @class([
 'h-0 mx-2',
 'bg-green-500' => $step > $index + 1,
 'bg-secondary-subtle' => $step <= $index + 1,
 ])></div>
 @endif
 </div>
 @endforeach
 </div>
 </div>

 <div class="row g-3">
 <!-- Main Content -->
 <div class="$1">
 <x-card>
 {{-- Step 1: Catamaran Selection --}}
 @if($step === 1)
 <div>
 <h2 class="h4 font-serif fw-semibold text-navy mb-2">
 Scegli il tuo catamarano
 </h2>
 <p class="text-secondary mb-5">
 Seleziona l'imbarcazione perfetta per la tua esperienza in mare.
 </p>

 <div class="">
 @foreach($catamarans as $catamaran)
 <div 
 wire:click="selectCatamaran({{ $catamaran->id }})"
 @class([
 'position-relative border border-2 rounded-4 p-4 ',
 'border-gold-500 bg-gold-50 ring-2 ring-gold-500' => $selectedCatamaran?->id === $catamaran->id,
 'border' => $selectedCatamaran?->id !== $catamaran->id,
 ])
 >
 <div class="d-flex flex-column $1 g-3">
 {{-- Image --}}
 <div class="w-100 $1 rounded-3 overflow-d-none bg-light flex-shrink-0">
 @if($catamaran->primaryImage)
 <img src="{{ $catamaran->primaryImage->url }}" 
 alt="{{ $catamaran->name }}"
 class="w-100 h-100 object-fit-cover">
 @else
 <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">
 <svg class="" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
 </svg>
 </div>
 @endif
 </div>
 
 {{-- Info --}}
 <div class="flex-grow-1">
 <h3 class="fs-5 fw-semibold text-navy">{{ $catamaran->name }}</h3>
 <p class="text-secondary small mt-1">{{ $catamaran->description_short }}</p>
 
 <div class="d-flex flex-wrap g-3 mt-3 small text-muted">
 <span class="d-flex align-items-center">
 <svg class="mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
 </svg>
 {{ $catamaran->capacity }} ospiti
 </span>
 @if($catamaran->length_meters)
 <span class="d-flex align-items-center">
 <svg class="mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
 </svg>
 {{ $catamaran->length_meters }}m
 </span>
 @endif
 </div>

 <div class="mt-4 d-flex items-baseline g-3">
 <span class="h4 fw-bold text-primary">
 €{{ number_format($catamaran->price_per_person_half_day, 0) }}
 </span>
 <span class="text-muted">/ persona (mezza giornata)</span>
 </div>
 </div>
 </div>

 @if($selectedCatamaran?->id === $catamaran->id)
 <div class="position-absolute top-3 right-3 bg-warning-subtle0 text-white rounded-pill p-1">
 <svg class="" fill="currentColor" viewBox="0 0 20 20">
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
 <h2 class="h4 font-serif fw-semibold text-navy mb-2">
 Seleziona data e orario
 </h2>
 <p class="text-secondary mb-5">
 Scegli quando vuoi vivere la tua esperienza su {{ $selectedCatamaran->name }}.
 </p>

 @if($prefillDateError)
 <x-alert type="warning" class="mb-4">
 {{ $prefillDateError }}
 </x-alert>
 @endif

 {{-- Calendar --}}
 <div class="mb-5">
 <livewire:public.booking-calendar 
 :catamaran-id="$selectedCatamaran->id"
 :available-dates="$availableDates"
 :selected-date="$date"
 />
 </div>

 {{-- Time Slots --}}
 @if($date)
 <div>
 <h3 class="fs-5 fw-semibold text-navy mb-4">
 Orari disponibili per {{ \Carbon\Carbon::parse($date)->locale('it')->isoFormat('dddd D MMMM') }}
 </h3>

 @if(count($availableSlots) > 0)
 <div class="row g-3">
 @foreach($availableSlots as $slotData)
 <div 
 wire:click="selectTimeSlot({{ $slotData['slot']->id }})"
 @class([
 'border border-2 rounded-xl p-4 ',
 'border-gold-500 bg-gold-50' => $timeSlotId === $slotData['slot']->id,
 'border' => $timeSlotId !== $slotData['slot']->id,
 ])
 >
 <div class="d-flex justify-content-between align-items-start">
 <div>
 <h4 class="fw-semibold text-navy">{{ $slotData['slot']->name }}</h4>
 <p class="small text-muted">
 {{ \Carbon\Carbon::parse($slotData['slot']->start_time)->format('H:i') }} - 
 {{ \Carbon\Carbon::parse($slotData['slot']->end_time)->format('H:i') }}
 </p>
 </div>
 <span class="small bg-success-subtle text-success px-2 py-1 rounded-pill">
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
 <h2 class="h4 font-serif fw-semibold text-navy mb-2">
 Tipo di prenotazione
 </h2>
 <p class="text-secondary mb-5">
 Scegli come vuoi prenotare la tua esperienza.
 </p>

 {{-- Booking Type --}}
 <div class="row g-3 mb-5">
 <div 
 wire:click="setBookingType('seats')"
 @class([
 'border border-2 rounded-xl p-6 ',
 'border-gold-500 bg-gold-50' => $bookingType === 'seats',
 'border' => $bookingType !== 'seats',
 ])
 >
 <div class="d-flex align-items-center mb-3">
 <svg class="text-primary me-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
 </svg>
 <h3 class="fw-semibold text-navy">Prenotazione Posti</h3>
 </div>
 <p class="small text-secondary">
 Prenota solo i posti che ti servono. Condividerai l'esperienza con altri ospiti.
 </p>
 <p class="mt-3 fs-5 fw-bold text-primary">
 Da €{{ number_format($selectedCatamaran->price_per_person_half_day, 0) }}/persona
 </p>
 </div>

 <div 
 wire:click="setBookingType('exclusive')"
 @class([
 'border border-2 rounded-xl p-6 position-relative overflow-d-none',
 'border-gold-500 bg-gold-50' => $bookingType === 'exclusive',
 'border' => $bookingType !== 'exclusive',
 ])
 >
 <div class="position-absolute top-0 end-0 bg-warning-subtle0 text-white small fw-bold px-3 py-1 rounded-bl-lg">
 ESCLUSIVO
 </div>
 <div class="d-flex align-items-center mb-3">
 <svg class="text-warning me-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
 </svg>
 <h3 class="fw-semibold text-navy">Escursione Privata</h3>
 </div>
 <p class="small text-secondary">
 L'intero catamarano solo per te e il tuo gruppo. Massima privacy e personalizzazione.
 </p>
 <p class="mt-3 fs-5 fw-bold text-warning">
 €{{ number_format($selectedCatamaran->exclusive_price_half_day, 0) }}
 </p>
 </div>
 </div>

 {{-- Number of Guests --}}
 @if($bookingType === 'seats')
 <div>
 <label class="d-block small fw-medium text-secondary mb-3">
 Numero di ospiti
 </label>
 <div class="d-flex align-items-center">
 <button 
 wire:click="updateSeats({{ $seats - 1 }})"
 class="rounded-pill border border border-2 d-flex align-items-center justify-content-center text-secondary disabled:opacity-50"
 @disabled($seats <= 1)
 >
 <svg class="" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
 </svg>
 </button>
 <span class="h3 fw-bold text-navy text-center">{{ $seats }}</span>
 <button 
 wire:click="updateSeats({{ $seats + 1 }})"
 class="rounded-pill border border border-2 d-flex align-items-center justify-content-center text-secondary disabled:opacity-50"
 @disabled($seats >= $availableSeats)
 >
 <svg class="" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
 </svg>
 </button>
 </div>
 <p class="small text-muted mt-2">
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
 <h2 class="h4 font-serif fw-semibold text-navy mb-2">
 Arricchisci la tua esperienza
 </h2>
 <p class="text-secondary mb-5">
 Aggiungi servizi extra per rendere la tua giornata ancora più speciale.
 </p>

 <div class="">
 @foreach($addons as $addon)
 <div 
 wire:click="toggleAddon({{ $addon->id }})"
 @class([
 'border border-2 rounded-xl p-4 ',
 'border-gold-500 bg-gold-50' => in_array($addon->id, $selectedAddons),
 'border' => !in_array($addon->id, $selectedAddons),
 ])
 >
 <div class="d-flex align-items-start justify-content-between">
 <div class="flex-grow-1">
 <div class="d-flex align-items-center">
 <h3 class="fw-semibold text-navy">{{ $addon->name }}</h3>
 @if($addon->requires_advance_booking)
 <span class="ms-2 small bg-warning-subtle text-warning px-2 py-0.5 rounded-pill">
 Prenotare {{ $addon->advance_hours }}h prima
 </span>
 @endif
 </div>
 <p class="small text-secondary mt-1">{{ $addon->description }}</p>
 </div>
 <div class="text-end ms-4">
 <p class="fs-5 fw-bold text-primary">
 €{{ number_format($addon->price, 2) }}
 </p>
 <p class="small text-muted">
 {{ $addon->price_type === 'per_person' ? '/ persona' : ($addon->price_type === 'per_day' ? '/ giorno' : 'totale') }}
 </p>
 </div>
 </div>
 
 {{-- Checkbox indicator --}}
 <div class="position-absolute top-4 right-4">
 <div @class([
 'w-6 h-6 rounded-full border border-2 d-flex align-items-center justify-content-center',
 'bg-gold-500 border-gold-500 text-white' => in_array($addon->id, $selectedAddons),
 'border-gray-300' => !in_array($addon->id, $selectedAddons),
 ])>
 @if(in_array($addon->id, $selectedAddons))
 <svg class="" fill="currentColor" viewBox="0 0 20 20">
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
 <h2 class="h4 font-serif fw-semibold text-navy mb-2">
 I tuoi dati
 </h2>
 <p class="text-secondary mb-5">
 Inserisci i dati per completare la prenotazione.
 </p>

 <form wire:submit="createBooking" class="">
 <div class="row g-3">
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

 <div class="row g-3">
 <x-input 
 type="tel"
 name="phone"
 wire:model="phone"
 label="Telefono"
 placeholder="+39 333 1234567"
 />
 <div>
 <label class="d-block small fw-medium text-secondary mb-1">Paese</label>
 <select wire:model="country" class="w-100 px-4 py-3 border rounded-3">
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
 <label class="d-block small fw-medium text-secondary mb-1">
 Richieste speciali (opzionale)
 </label>
 <textarea 
 wire:model="specialRequests"
 rows="3"
 class="w-100 px-4 py-3 border rounded-3"
 placeholder="Allergie alimentari, occasioni speciali, richieste particolari..."
 ></textarea>
 </div>

 {{-- Terms --}}
 <div class="pt-4 border-top">
 <label class="d-flex align-items-start">
 <input type="checkbox" wire:model="acceptTerms" class="mt-1 text-primary rounded">
 <span class="ms-3 small text-secondary">
 Ho letto e accetto i <a href="{{ route('terms') }}" target="_blank" class="text-primary">Termini e Condizioni</a> *
 </span>
 </label>
 @error('acceptTerms')
 <p class="small text-danger">{{ $message }}</p>
 @enderror

 <label class="d-flex align-items-start">
 <input type="checkbox" wire:model="acceptPrivacy" class="mt-1 text-primary rounded">
 <span class="ms-3 small text-secondary">
 Acconsento al trattamento dei miei dati secondo la <a href="{{ route('privacy') }}" target="_blank" class="text-primary">Privacy Policy</a> *
 </span>
 </label>
 @error('acceptPrivacy')
 <p class="small text-danger">{{ $message }}</p>
 @enderror
 </div>
 </form>
 </div>
 @endif

 {{-- Navigation Buttons --}}
 <div class="d-flex justify-content-between mt-5 pt-4 border-top">
 @if($step > 1)
 <x-button type="secondary" wire:click="previousStep">
 <svg class="me-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
 <svg class="ms-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
 <svg class="ms-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
 </svg>
 </span>
 <span wire:loading>
 <svg class="animate-spin me-2" fill="none" viewBox="0 0 24 24">
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
 <div class="$1">
 <div class="position-sticky top-24">
 <x-card>
 <h3 class="fs-5 fw-semibold text-navy mb-4">Riepilogo prenotazione</h3>

 @if($selectedCatamaran)
 <div class="">
 {{-- Catamaran --}}
 <div class="d-flex align-items-center pb-4 border-bottom">
 <div class="rounded-3 overflow-d-none bg-light me-3">
 @if($selectedCatamaran->primaryImage)
 <img src="{{ $selectedCatamaran->primaryImage->url }}" alt="{{ $selectedCatamaran->name }}" class="w-100 h-100 object-fit-cover">
 @endif
 </div>
 <div>
 <p class="fw-semibold text-navy">{{ $selectedCatamaran->name }}</p>
 <p class="small text-muted">{{ $selectedCatamaran->capacity }} ospiti max</p>
 </div>
 </div>

 {{-- Date & Time --}}
 @if($date && $selectedTimeSlot)
 <div class="pb-4 border-bottom">
 <p class="small text-muted">Data e orario</p>
 <p class="fw-semibold text-navy">
 {{ \Carbon\Carbon::parse($date)->locale('it')->isoFormat('dddd D MMMM YYYY') }}
 </p>
 <p class="small text-secondary">
 {{ $selectedTimeSlot->name }} ({{ \Carbon\Carbon::parse($selectedTimeSlot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($selectedTimeSlot->end_time)->format('H:i') }})
 </p>
 </div>
 @endif

 {{-- Guests --}}
 @if($step >= 3)
 <div class="pb-4 border-bottom">
 <p class="small text-muted">Tipo prenotazione</p>
 <p class="fw-semibold text-navy">
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
 <div class="">
 <div class="d-flex justify-content-between small">
 <span class="text-secondary">
 @if($bookingType === 'exclusive')
 Escursione privata
 @else
 {{ $seats }} x €{{ number_format($basePrice / $seats, 2) }}
 @endif
 </span>
 <span class="text-dark">€{{ number_format($basePrice, 2) }}</span>
 </div>

 @if($addonsTotal > 0)
 <div class="d-flex justify-content-between small">
 <span class="text-secondary">Extra</span>
 <span class="text-dark">€{{ number_format($addonsTotal, 2) }}</span>
 </div>
 @endif

 @if($discountAmount > 0)
 <div class="d-flex justify-content-between small text-success">
 <span>Sconto</span>
 <span>-€{{ number_format($discountAmount, 2) }}</span>
 </div>
 @endif

 <div class="d-flex justify-content-between small">
 <span class="text-secondary">IVA ({{ config('booking.tax_rate', 22) }}%)</span>
 <span class="text-dark">€{{ number_format($taxAmount, 2) }}</span>
 </div>

 <div class="d-flex justify-content-between pt-3 border-top mt-3">
 <span class="fw-semibold text-navy">Totale</span>
 <span class="fs-5 fw-bold text-primary">€{{ number_format($totalAmount, 2) }}</span>
 </div>
 </div>

 {{-- Discount Code --}}
 @if($step >= 4)
 <div class="pt-4 border-top mt-4">
 @if(!$appliedDiscount)
 <div class="d-flex g-3">
 <input 
 type="text" 
 wire:model="discountCode"
 placeholder="Codice sconto"
 class="flex-grow-1 px-3 py-2 small border rounded-3"
 >
 <button 
 wire:click="applyDiscountCode"
 class="px-4 py-2 small fw-medium text-primary border border-primary rounded-3"
 >
 Applica
 </button>
 </div>
 @if($discountError)
 <p class="small text-danger mt-1">{{ $discountError }}</p>
 @endif
 @else
 <div class="d-flex align-items-center justify-content-between bg-success-subtle border border-success rounded-3 px-3 py-2">
 <div>
 <p class="small fw-medium text-success">{{ $appliedDiscount->code }}</p>
 <p class="small text-success">Sconto applicato</p>
 </div>
 <button wire:click="removeDiscount" class="text-success">
 <svg class="" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
 <p class="text-muted small">
 Seleziona un catamarano per vedere il riepilogo della prenotazione.
 </p>
 @endif
 </x-card>

 {{-- Trust Badges --}}
 <div class="mt-4">
 <div class="d-flex align-items-center small text-secondary">
 <svg class="me-2 text-success" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
 </svg>
 Pagamento sicuro con Stripe
 </div>
 <div class="d-flex align-items-center small text-secondary">
 <svg class="me-2 text-success" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
 </svg>
 Cancellazione gratuita fino a 48h prima
 </div>
 <div class="d-flex align-items-center small text-secondary">
 <svg class="me-2 text-success" fill="currentColor" viewBox="0 0 20 20">
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
