@props([
    'padding' => true,
    'shadow' => 'lg',
])

@php
    $shadows = [
        'none' => '',
        'sm' => 'shadow-sm',
        'md' => 'shadow-md',
        'lg' => 'shadow-lg',
        'xl' => 'shadow-xl',
        '2xl' => 'shadow-2xl',
    ];
@endphp

<div {{ $attributes->merge([
    'class' => 'bg-white rounded-2xl ' . 
               ($shadows[$shadow] ?? $shadows['lg']) . 
               ($padding ? ' p-6 lg:p-8' : '')
]) }}>
    {{ $slot }}
</div>
