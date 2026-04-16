@extends('layouts.admin')

@section('title', 'Codici Sconto')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Codici Sconto</h1>
                <p class="text-gray-600">Gestisci i codici promozionali</p>
            </div>
            <a href="{{ route('admin.discounts.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nuovo Codice
            </a>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-sm text-gray-500">Totali</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-sm text-gray-500">Attivi</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-sm text-gray-500">Scaduti</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['expired'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <p class="text-sm text-gray-500">Utilizzi Totali</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['total_usage'] }}</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <form action="{{ route('admin.discounts.index') }}" method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cerca codice..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Tutti gli stati</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Attivi</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inattivi</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Scaduti</option>
                        <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Futuri</option>
                    </select>
                </div>
                <div>
                    <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Tutti i tipi</option>
                        <option value="percentage" {{ request('type') === 'percentage' ? 'selected' : '' }}>Percentuale</option>
                        <option value="fixed" {{ request('type') === 'fixed' ? 'selected' : '' }}>Fisso</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Filtra
                </button>
                @if(request()->hasAny(['search', 'status', 'type']))
                    <a href="{{ route('admin.discounts.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700 transition-colors">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        {{-- Discounts Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Codice
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Sconto
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Validità
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Utilizzi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Stato
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Azioni
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($discounts as $discount)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-mono font-semibold text-gray-900">{{ $discount->code }}</p>
                                        @if($discount->description)
                                            <p class="text-sm text-gray-500">{{ Str::limit($discount->description, 40) }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($discount->discount_type === 'percentage')
                                        <span class="text-lg font-semibold text-gray-900">{{ $discount->discount_value }}%</span>
                                    @else
                                        <span class="text-lg font-semibold text-gray-900">€{{ number_format($discount->discount_value, 2, ',', '.') }}</span>
                                    @endif
                                    @if($discount->min_amount)
                                        <p class="text-xs text-gray-500">Min. €{{ number_format($discount->min_amount, 2, ',', '.') }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($discount->valid_from || $discount->valid_until)
                                        <div class="space-y-1">
                                            @if($discount->valid_from)
                                                <p class="text-gray-600">Da: {{ $discount->valid_from->format('d/m/Y') }}</p>
                                            @endif
                                            @if($discount->valid_until)
                                                <p class="{{ $discount->valid_until->isPast() ? 'text-red-600' : 'text-gray-600' }}">
                                                    A: {{ $discount->valid_until->format('d/m/Y') }}
                                                </p>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">Sempre valido</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-gray-900">{{ $discount->usage_count }}</span>
                                        @if($discount->usage_limit)
                                            <span class="text-gray-400">/ {{ $discount->usage_limit }}</span>
                                        @else
                                            <span class="text-gray-400">/ ∞</span>
                                        @endif
                                    </div>
                                    @if($discount->usage_limit)
                                        <div class="w-20 h-1.5 bg-gray-100 rounded-full mt-1">
                                            <div class="h-full bg-primary-600 rounded-full" 
                                                 style="width: {{ min(100, ($discount->usage_count / $discount->usage_limit) * 100) }}%"></div>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $isExpired = $discount->valid_until && $discount->valid_until->isPast();
                                        $isUpcoming = $discount->valid_from && $discount->valid_from->isFuture();
                                        $isExhausted = $discount->usage_limit && $discount->usage_count >= $discount->usage_limit;
                                    @endphp
                                    
                                    @if(!$discount->is_active)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800 rounded-full">
                                            Inattivo
                                        </span>
                                    @elseif($isExpired)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">
                                            Scaduto
                                        </span>
                                    @elseif($isExhausted)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold bg-orange-100 text-orange-800 rounded-full">
                                            Esaurito
                                        </span>
                                    @elseif($isUpcoming)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
                                            Futuro
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                            Attivo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('admin.discounts.toggle', $discount) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    class="p-2 text-gray-400 hover:text-yellow-600 transition-colors"
                                                    title="{{ $discount->is_active ? 'Disattiva' : 'Attiva' }}">
                                                @if($discount->is_active)
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.discounts.show', $discount) }}" 
                                           class="p-2 text-gray-400 hover:text-primary-600 transition-colors"
                                           title="Dettagli">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.discounts.edit', $discount) }}" 
                                           class="p-2 text-gray-400 hover:text-primary-600 transition-colors"
                                           title="Modifica">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.discounts.destroy', $discount) }}" 
                                              method="POST"
                                              onsubmit="return confirm('Sei sicuro di voler eliminare questo codice sconto?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                                                    title="Elimina">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <p class="text-gray-500 mb-4">Nessun codice sconto trovato</p>
                                    <a href="{{ route('admin.discounts.create') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                                        Crea il primo codice
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($discounts->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $discounts->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
