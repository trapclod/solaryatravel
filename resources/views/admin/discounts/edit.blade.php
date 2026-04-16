@extends('layouts.admin')

@section('title', 'Modifica Codice Sconto')

@section('content')
    <div class="max-w-3xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.discounts.index') }}" 
               class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Modifica Codice Sconto</h1>
                <p class="text-gray-600 font-mono">{{ $discount->code }}</p>
            </div>
        </div>

        <form action="{{ route('admin.discounts.update', $discount) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            @include('admin.discounts._form')

            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.discounts.index') }}" 
                   class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Annulla
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                    Salva Modifiche
                </button>
            </div>
        </form>
    </div>
@endsection
