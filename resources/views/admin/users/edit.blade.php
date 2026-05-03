@extends('layouts.admin')

@section('title', 'Modifica Utente')

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
            <h1 class="text-2xl font-bold text-gray-900">Modifica Utente</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $user->name }} — {{ $user->email }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm space-y-1">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf
        @method('PUT')

        {{-- Nome --}}
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nome completo <span class="text-red-500">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('name') border-red-300 @enderror">
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email <span class="text-red-500">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('email') border-red-300 @enderror">
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Telefono --}}
        <div>
            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1.5">Telefono</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
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
                    <option value="{{ $value }}" {{ old('role', $user->role) === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('role')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password (opzionale) --}}
        <div class="pt-2 border-t border-gray-100">
            <p class="text-xs text-gray-400 mb-3">Lascia vuoto per mantenere la password attuale</p>

            <div class="space-y-4">
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Nuova Password</label>
                    <input type="password" id="password" name="password" minlength="8"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('password') border-red-300 @enderror">
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Conferma nuova password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" minlength="8"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                </div>
            </div>
        </div>

        {{-- Buttons --}}
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold">
                Salva Modifiche
            </button>
            <a href="{{ route('admin.users.index') }}" class="btn-secondary px-6 py-2.5 rounded-xl text-sm font-semibold">
                Annulla
            </a>
        </div>
    </form>
</div>
@endsection
