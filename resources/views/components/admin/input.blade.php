@props([
    'label' => null,
    'type' => 'text',
    'name',
    'id' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'hint' => null,
    'prefix' => null,
    'suffix' => null,
])

@php
    $inputId = $id ?? $name;
    $hasError = $error || $errors->has($name);
@endphp

<div {{ $attributes->only(['class'])->merge(['class' => '']) }}>
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-gray-700 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        @if($prefix)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-gray-500 text-sm">{{ $prefix }}</span>
            </div>
        @endif
        
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $inputId }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->except(['class']) }}
            @class([
                'block w-full rounded-xl border-0 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset placeholder:text-gray-400 focus:ring-2 focus:ring-inset sm:text-sm transition-all duration-200',
                'ring-gray-300 focus:ring-primary-500' => !$hasError,
                'ring-red-300 focus:ring-red-500 text-red-900 placeholder:text-red-300' => $hasError,
                'pl-10' => $prefix,
                'pr-10' => $suffix,
                'disabled:bg-gray-50 disabled:text-gray-500 disabled:cursor-not-allowed' => $disabled,
            ])
        >
        
        @if($suffix)
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span class="text-gray-500 text-sm">{{ $suffix }}</span>
            </div>
        @endif
    </div>
    
    @if($hasError)
        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            {{ $error ?? $errors->first($name) }}
        </p>
    @elseif($hint)
        <p class="mt-1.5 text-sm text-gray-500">{{ $hint }}</p>
    @endif
</div>
