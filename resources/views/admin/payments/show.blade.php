@extends('layouts.admin')

@section('title', 'Dettagli Pagamento')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.payments.index') }}" 
                   class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Pagamento #{{ substr($payment->uuid, 0, 8) }}</h1>
                    <p class="text-gray-600">Dettagli della transazione</p>
                </div>
            </div>
            <div>
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'processing' => 'bg-blue-100 text-blue-800',
                        'succeeded' => 'bg-green-100 text-green-800',
                        'failed' => 'bg-red-100 text-red-800',
                        'cancelled' => 'bg-gray-100 text-gray-800',
                        'refunded' => 'bg-purple-100 text-purple-800',
                        'partially_refunded' => 'bg-orange-100 text-orange-800',
                    ];
                    $colorClass = $statusColors[$payment->status->value] ?? 'bg-gray-100 text-gray-800';
                @endphp
                <span class="px-4 py-2 text-sm font-semibold rounded-full {{ $colorClass }}">
                    {{ $payment->status->label() }}
                </span>
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

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center gap-2 text-red-800">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Payment Details --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Amount Card --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Importo</h2>
                    <div class="flex items-end gap-4">
                        <div>
                            <p class="text-4xl font-bold text-gray-900">€{{ number_format($payment->amount, 2, ',', '.') }}</p>
                            <p class="text-sm text-gray-500">{{ strtoupper($payment->currency) }}</p>
                        </div>
                        @if($payment->refunded_amount > 0)
                            <div class="text-right">
                                <p class="text-xl font-bold text-red-600">-€{{ number_format($payment->refunded_amount, 2, ',', '.') }}</p>
                                <p class="text-sm text-gray-500">Rimborsato</p>
                            </div>
                        @endif
                    </div>
                    @if($payment->fee_amount)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Commissioni</span>
                                <span class="text-gray-900">€{{ number_format($payment->fee_amount, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm mt-1">
                                <span class="text-gray-500">Netto</span>
                                <span class="font-medium text-gray-900">€{{ number_format($payment->net_amount, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Transaction Details --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Dettagli Transazione</h2>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Gateway</dt>
                            <dd class="font-medium text-gray-900">{{ ucfirst($payment->gateway) }}</dd>
                        </div>
                        @if($payment->gateway_payment_id)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Payment ID</dt>
                                <dd class="font-mono text-sm text-gray-900">{{ $payment->gateway_payment_id }}</dd>
                            </div>
                        @endif
                        @if($payment->gateway_transaction_id)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Transaction ID</dt>
                                <dd class="font-mono text-sm text-gray-900">{{ $payment->gateway_transaction_id }}</dd>
                            </div>
                        @endif
                        @if($payment->card_brand || $payment->card_last_four)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Carta</dt>
                                <dd class="font-medium text-gray-900">
                                    {{ ucfirst($payment->card_brand ?? 'Carta') }} •••• {{ $payment->card_last_four }}
                                </dd>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Data Creazione</dt>
                            <dd class="text-gray-900">{{ $payment->created_at->format('d/m/Y H:i:s') }}</dd>
                        </div>
                        @if($payment->paid_at)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Data Pagamento</dt>
                                <dd class="text-gray-900">{{ $payment->paid_at->format('d/m/Y H:i:s') }}</dd>
                            </div>
                        @endif
                        @if($payment->refunded_at)
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Data Rimborso</dt>
                                <dd class="text-gray-900">{{ $payment->refunded_at->format('d/m/Y H:i:s') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                @if($payment->failure_reason)
                    <div class="bg-red-50 rounded-xl border border-red-200 p-6">
                        <h2 class="text-lg font-semibold text-red-800 mb-2">Motivo del Fallimento</h2>
                        <p class="text-red-700">{{ $payment->failure_reason }}</p>
                    </div>
                @endif

                @if($payment->refund_reason)
                    <div class="bg-purple-50 rounded-xl border border-purple-200 p-6">
                        <h2 class="text-lg font-semibold text-purple-800 mb-2">Motivo del Rimborso</h2>
                        <p class="text-purple-700">{{ $payment->refund_reason }}</p>
                    </div>
                @endif

                {{-- Gateway Response --}}
                @if($payment->gateway_response)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Risposta Gateway</h2>
                        <pre class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 overflow-x-auto">{{ json_encode($payment->gateway_response, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Linked Booking --}}
                @if($payment->booking)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Prenotazione</h2>
                        <div class="space-y-3">
                            <a href="{{ route('admin.bookings.show', $payment->booking) }}" 
                               class="block text-primary-600 hover:text-primary-800 font-semibold text-lg">
                                {{ $payment->booking->booking_number }}
                            </a>
                            <div class="text-sm text-gray-600">
                                <p><strong>Catamarano:</strong> {{ $payment->booking->catamaran->name ?? '-' }}</p>
                                <p><strong>Data:</strong> {{ $payment->booking->booking_date->format('d/m/Y') }}</p>
                                <p><strong>Orario:</strong> {{ $payment->booking->timeSlot->name ?? '-' }}</p>
                                <p><strong>Posti:</strong> {{ $payment->booking->seats }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Cliente</h2>
                        <div class="space-y-2 text-sm">
                            <p class="font-medium text-gray-900">
                                {{ $payment->booking->customer_first_name }} {{ $payment->booking->customer_last_name }}
                            </p>
                            <p class="text-gray-600">{{ $payment->booking->customer_email }}</p>
                            @if($payment->booking->customer_phone)
                                <p class="text-gray-600">{{ $payment->booking->customer_phone }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Refund Action --}}
                @if(in_array($payment->status, [\App\Enums\PaymentStatus::SUCCEEDED, \App\Enums\PaymentStatus::PARTIALLY_REFUNDED]))
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Rimborso</h2>
                        
                        @php
                            $maxRefund = $payment->amount - $payment->refunded_amount;
                        @endphp

                        @if($maxRefund > 0)
                            <form action="{{ route('admin.payments.refund', $payment) }}" method="POST" 
                                  onsubmit="return confirm('Sei sicuro di voler processare questo rimborso?');">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Importo (max €{{ number_format($maxRefund, 2, ',', '.') }})
                                        </label>
                                        <input type="number" name="amount" step="0.01" min="0.01" max="{{ $maxRefund }}"
                                               value="{{ $maxRefund }}"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                               required>
                                        @error('amount')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Motivo
                                        </label>
                                        <textarea name="reason" rows="3"
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                                                  placeholder="Inserisci il motivo del rimborso..."
                                                  required>{{ old('reason') }}</textarea>
                                        @error('reason')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <button type="submit" 
                                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                        Processa Rimborso
                                    </button>
                                </div>
                            </form>
                        @else
                            <p class="text-gray-500 text-sm">Questo pagamento è già stato completamente rimborsato.</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
