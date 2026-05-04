@props([
    'type' => 'primary',
    'size' => 'md',
    'href' => null,
    'disabled' => false,
])

@php
    $variants = [
        'primary'   => 'btn-gold',
        'secondary' => 'btn-secondary',
        'outline'   => 'btn-outline-primary',
        'ghost'     => 'btn-link text-decoration-none text-secondary',
        'danger'    => 'btn-danger',
        'success'   => 'btn-success',
    ];
    $sizes = [
        'sm' => 'btn-sm',
        'md' => '',
        'lg' => 'btn-lg',
        'xl' => 'btn-lg fs-5 px-5 py-3',
    ];
    $classes = trim('btn rounded-pill shadow-sm ' . ($variants[$type] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? ''));
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
