@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'icon' => false,
    'loading' => false,
    'disabled' => false,
])

@php
    $variants = [
        'primary'   => 'btn-primary',
        'secondary' => 'btn-secondary',
        'danger'    => 'btn-danger',
        'success'   => 'btn-success',
        'ghost'     => 'btn-link text-decoration-none text-secondary',
        'outline'   => 'btn-outline-secondary',
        'gold'      => 'btn-gold',
    ];
    $sizes = [ 'sm' => 'btn-sm', 'md' => '', 'lg' => 'btn-lg' ];
    $classes = trim('btn rounded-3 fw-medium ' . ($variants[$variant] ?? 'btn-primary') . ' ' . ($sizes[$size] ?? ''));
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($loading)<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @if($disabled || $loading) disabled @endif>
        @if($loading)<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>@endif
        {{ $slot }}
    </button>
@endif
