@props([
    'padding' => true,
    'shadow' => 'lg',
])

@php
    $shadows = [
        'none' => '',
        'sm'   => 'shadow-sm',
        'md'   => 'shadow',
        'lg'   => 'shadow',
        'xl'   => 'shadow-lg',
        '2xl'  => 'shadow-lg',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'card border-0 ' . ($shadows[$shadow] ?? 'shadow')]) }}>
    <div class="{{ $padding ? 'card-body p-4 p-lg-5' : 'card-body p-0' }}">
        {{ $slot }}
    </div>
</div>
