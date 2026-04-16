@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, danger, success, ghost, outline
    'size' => 'md', // sm, md, lg
    'href' => null,
    'icon' => false,
    'loading' => false,
    'disabled' => false,
])

@php
    $variants = [
        'primary' => 'bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white shadow-sm hover:shadow-md',
        'secondary' => 'bg-gray-100 hover:bg-gray-200 text-gray-700',
        'danger' => 'bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white shadow-sm hover:shadow-md',
        'success' => 'bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white shadow-sm hover:shadow-md',
        'ghost' => 'hover:bg-gray-100 text-gray-700',
        'outline' => 'border-2 border-gray-300 hover:border-gray-400 text-gray-700 hover:bg-gray-50',
        'gold' => 'bg-gradient-to-r from-gold-400 to-gold-500 hover:from-gold-500 hover:to-gold-600 text-navy-900 shadow-sm hover:shadow-md',
    ];
    
    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-6 py-3 text-base',
    ];
    
    $iconSizes = [
        'sm' => 'p-1.5',
        'md' => 'p-2',
        'lg' => 'p-3',
    ];
    
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed';
    $sizeClass = $icon ? $iconSizes[$size] : $sizes[$size];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClasses . ' ' . $variants[$variant] . ' ' . $sizeClass]) }}>
        @if($loading)
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $baseClasses . ' ' . $variants[$variant] . ' ' . $sizeClass]) }} @if($disabled || $loading) disabled @endif>
        @if($loading)
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif
