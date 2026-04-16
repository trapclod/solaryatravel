@props([
    'type' => 'default', // success, warning, danger, info, default, primary
    'size' => 'md', // sm, md, lg
    'dot' => false,
    'pulse' => false,
])

@php
    $colors = [
        'success' => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
        'warning' => 'bg-amber-100 text-amber-800 ring-amber-600/20',
        'danger' => 'bg-red-100 text-red-800 ring-red-600/20',
        'info' => 'bg-blue-100 text-blue-800 ring-blue-600/20',
        'default' => 'bg-gray-100 text-gray-800 ring-gray-600/20',
        'primary' => 'bg-primary-100 text-primary-800 ring-primary-600/20',
    ];
    
    $sizes = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-1 text-xs',
        'lg' => 'px-3 py-1.5 text-sm',
    ];
    
    $dotColors = [
        'success' => 'bg-emerald-500',
        'warning' => 'bg-amber-500',
        'danger' => 'bg-red-500',
        'info' => 'bg-blue-500',
        'default' => 'bg-gray-500',
        'primary' => 'bg-primary-500',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 font-medium rounded-full ring-1 ring-inset ' . $colors[$type] . ' ' . $sizes[$size]]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotColors[$type] }} {{ $pulse ? 'animate-pulse' : '' }}"></span>
    @endif
    {{ $slot }}
</span>
