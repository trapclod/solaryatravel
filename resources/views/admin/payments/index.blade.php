@extends('layouts.admin')

@section('title', 'Pagamenti')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pagamenti</h1>
                <p class="text-gray-600">Gestione transazioni e rimborsi</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-sm text-gray-500">Totale Pagamenti</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-sm text-gray-500">Completati</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['succeeded'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-sm text-gray-500">In Attesa</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-sm text-gray-500">Incassato</p>
                <p class="text-2xl font-bold text-primary-600">€{{ number_format($stats['total_amount'], 2, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-sm text-gray-500">Rimborsato</p>
                <p class="text-2xl font-bold text-red-600">€{{ number_format($stats['refunded_amount'], 2, ',', '.') }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <form action="{{ route('admin.payments.index') }}" method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cerca per ID pagamento, prenotazione..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Tutti gli stati</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="gateway" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Tutti i gateway</option>
                        <option value="stripe" {{ request('gateway') === 'stripe' ? 'selected' : '' }}>Stripe</option>
                        <option value="paypal" {{ request('gateway') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="from" value="{{ request('from') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Da">
                </div>
                <div>
                    <input type="date" name="to" value="{{ request('to') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="A">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        Filtra
                    </button>
                    @if(request()->hasAny(['search', 'status', 'gateway', 'from', 'to']))
                        <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Payments Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Prenotazione</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Gateway</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Importo</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Stato</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Azioni</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $payment->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($payment->booking)
                                        <a href="{{ route('admin.bookings.show', $payment->booking) }}" 
                                           class="text-primary-600 hover:text-primary-800 font-medium">
                                            {{ $payment->booking->booking_number }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($payment->booking)
                                        <div class="text-sm">
                                            <p class="font-medium text-gray-900">
                                                {{ $payment->booking->customer_first_name }} {{ $payment->booking->customer_last_name }}
                                            </p>
                                            <p class="text-gray-500">{{ $payment->booking->customer_email }}</p>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($payment->gateway === 'stripe')
                                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.594-7.305h.003z"/>
                                            </svg>
                                        @else
                                            <span class="text-gray-600 text-sm">{{ ucfirst($payment->gateway) }}</span>
                                        @endif
                                        @if($payment->card_last_four)
                                            <span class="text-xs text-gray-500">•••• {{ $payment->card_last_four }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <p class="font-semibold text-gray-900">€{{ number_format($payment->amount, 2, ',', '.') }}</p>
                                    @if($payment->refunded_amount > 0)
                                        <p class="text-xs text-red-600">-€{{ number_format($payment->refunded_amount, 2, ',', '.') }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
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
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $colorClass }}">
                                        {{ $payment->status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.payments.show', $payment) }}" 
                                       class="text-primary-600 hover:text-primary-800">
                                        Dettagli
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    Nessun pagamento trovato
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
