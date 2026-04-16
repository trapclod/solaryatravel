@props([
    'type' => 'primary',
    'size' => 'md',
    'href' => null,
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
    
    $types = [
        'primary' => 'btn-gold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5',
        'secondary' => 'btn-secondary',
        'outline' => 'bg-transparent border-2 border-sky-500 text-sky-600 hover:bg-sky-50 focus:ring-sky-500',
        'ghost' => 'bg-transparent text-gray-600 hover:bg-gray-100 focus:ring-gray-500',
        'danger' => 'btn-danger shadow-lg hover:shadow-xl',
        'success' => 'btn-success shadow-lg hover:shadow-xl',
    ];
    
    $sizes = [
        'sm' => 'px-4 py-2 text-sm',
        'md' => 'px-6 py-3 text-base',
        'lg' => 'px-8 py-4 text-lg',
        'xl' => 'px-10 py-5 text-xl',
    ];
    
    $classes = $baseClasses . ' ' . ($types[$type] ?? $types['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $classes, 'disabled' => $disabled]) }}>
        {{ $slot }}
    </button>
@endif
