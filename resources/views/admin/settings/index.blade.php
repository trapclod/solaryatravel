@extends('layouts.admin')

@section('title', 'Impostazioni')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Impostazioni</h1>
                <p class="text-gray-600">Configurazione generale del sistema</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.settings.timeslots') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Gestisci Fasce Orarie
                </a>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center gap-2 text-green-800">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf

            {{-- General Settings --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Informazioni Generali</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome Sito</label>
                        <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name']) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               required>
                        @error('site_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="site_email" value="{{ old('site_email', $settings['site_email']) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               required>
                        @error('site_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefono</label>
                        <input type="text" name="site_phone" value="{{ old('site_phone', $settings['site_phone'] ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('site_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Indirizzo</label>
                        <input type="text" name="site_address" value="{{ old('site_address', $settings['site_address'] ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('site_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ragione Sociale</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">P.IVA</label>
                        <input type="text" name="vat_number" value="{{ old('vat_number', $settings['vat_number'] ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('vat_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Booking Settings --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Impostazioni Prenotazioni</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valuta</label>
                        <select name="currency"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="EUR" {{ old('currency', $settings['currency']) === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                            <option value="USD" {{ old('currency', $settings['currency']) === 'USD' ? 'selected' : '' }}>USD ($)</option>
                            <option value="GBP" {{ old('currency', $settings['currency']) === 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                        </select>
                        @error('currency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Aliquota IVA (%)</label>
                        <input type="number" name="tax_rate" step="0.01" min="0" max="100"
                               value="{{ old('tax_rate', $settings['tax_rate']) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               required>
                        @error('tax_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Posti Default</label>
                        <input type="number" name="default_seats" min="1" max="50"
                               value="{{ old('default_seats', $settings['default_seats']) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               required>
                        @error('default_seats')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prenotazione Anticipata (giorni)</label>
                        <input type="number" name="booking_advance_days" min="0" max="365"
                               value="{{ old('booking_advance_days', $settings['booking_advance_days']) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               required>
                        @error('booking_advance_days')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cancellazione Gratuita (ore)</label>
                        <input type="number" name="cancellation_hours" min="0" max="168"
                               value="{{ old('cancellation_hours', $settings['cancellation_hours']) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               required>
                        @error('cancellation_hours')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Scadenza Pagamento (minuti)</label>
                        <input type="number" name="payment_deadline_minutes" min="5" max="1440"
                               value="{{ old('payment_deadline_minutes', $settings['payment_deadline_minutes']) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               required>
                        @error('payment_deadline_minutes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Stripe Settings --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Configurazione Stripe</h2>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chiave Pubblica (Publishable Key)</label>
                        <input type="text" name="stripe_public_key" 
                               value="{{ old('stripe_public_key', $settings['stripe_public_key'] ?? '') }}"
                               placeholder="pk_live_..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-mono text-sm">
                        @error('stripe_public_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chiave Segreta (Secret Key)</label>
                        <input type="password" name="stripe_secret_key" 
                               value="{{ old('stripe_secret_key', $settings['stripe_secret_key'] ?? '') }}"
                               placeholder="sk_live_..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-mono text-sm">
                        <p class="mt-1 text-xs text-gray-500">Non verrà mostrata per sicurezza</p>
                        @error('stripe_secret_key')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Webhook Secret</label>
                        <input type="password" name="stripe_webhook_secret" 
                               value="{{ old('stripe_webhook_secret', $settings['stripe_webhook_secret'] ?? '') }}"
                               placeholder="whsec_..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-mono text-sm">
                        @error('stripe_webhook_secret')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Email Settings --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Configurazione Email (SMTP)</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Host SMTP</label>
                        <input type="text" name="smtp_host" 
                               value="{{ old('smtp_host', $settings['smtp_host'] ?? '') }}"
                               placeholder="smtp.example.com"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('smtp_host')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Porta SMTP</label>
                        <input type="number" name="smtp_port" min="1" max="65535"
                               value="{{ old('smtp_port', $settings['smtp_port'] ?? 587) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('smtp_port')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username SMTP</label>
                        <input type="text" name="smtp_username" 
                               value="{{ old('smtp_username', $settings['smtp_username'] ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('smtp_username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password SMTP</label>
                        <input type="password" name="smtp_password" 
                               value="{{ old('smtp_password', $settings['smtp_password'] ?? '') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('smtp_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Crittografia</label>
                        <select name="smtp_encryption"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="tls" {{ old('smtp_encryption', $settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('smtp_encryption', $settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                        @error('smtp_encryption')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome Mittente</label>
                        <input type="text" name="mail_from_name" 
                               value="{{ old('mail_from_name', $settings['mail_from_name'] ?? '') }}"
                               placeholder="Solarya Travel"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('mail_from_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Mittente</label>
                        <input type="email" name="mail_from_address" 
                               value="{{ old('mail_from_address', $settings['mail_from_address'] ?? '') }}"
                               placeholder="noreply@solaryatravel.com"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('mail_from_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- System Settings --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Opzioni Sistema</h2>
                <div class="space-y-4">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="enable_notifications" value="1"
                               {{ old('enable_notifications', $settings['enable_notifications'] ?? true) ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <div>
                            <span class="text-gray-900 font-medium">Abilita Notifiche Email</span>
                            <p class="text-sm text-gray-500">Invia email di conferma prenotazione e promemoria</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="maintenance_mode" value="1"
                               {{ old('maintenance_mode', $settings['maintenance_mode'] ?? false) ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <div>
                            <span class="text-gray-900 font-medium">Modalità Manutenzione</span>
                            <p class="text-sm text-gray-500">Il sito pubblico mostrerà una pagina di manutenzione</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex justify-end">
                <button type="submit" 
                        class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium">
                    Salva Impostazioni
                </button>
            </div>
        </form>
    </div>
@endsection
