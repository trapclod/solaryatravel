@extends('layouts.admin')

@section('title', 'Nuovo Utente')

@section('content')
<div class="p-6 max-w-2xl">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.users.index') }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nuovo Utente</h1>
            <p class="text-sm text-gray-500 mt-0.5">Crea un nuovo account utente</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf

        {{-- Nome --}}
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nome completo <span class="text-red-500">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('name') border-red-300 @enderror">
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('email') border-red-300 @enderror">
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Telefono --}}
        <div>
            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1.5">Telefono</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('phone') border-red-300 @enderror">
            @error('phone')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Ruolo --}}
        <div>
            <label for="role" class="block text-sm font-semibold text-gray-700 mb-1.5">Ruolo <span class="text-red-500">*</span></label>
            <select id="role" name="role" required
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none bg-white @error('role') border-red-300 @enderror">
                @foreach($roles as $value => $label)
                    <option value="{{ $value }}" {{ old('role', 'customer') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('role')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password <span class="text-red-500">*</span></label>
            <input type="password" id="password" name="password" required minlength="8"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('password') border-red-300 @enderror">
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Conferma password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Conferma password <span class="text-red-500">*</span></label>
            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
        </div>

        {{-- Buttons --}}
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold">
                Crea Utente
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary px-6 py-2.5 rounded-xl text-sm font-semibold">
                Annulla
            </a>
        </div>
    </form>
</div>
@endsection
