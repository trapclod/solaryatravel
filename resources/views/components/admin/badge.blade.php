@props([
    'type' => 'default',
    'size' => 'md',
    'dot' => false,
    'pulse' => false,
])

@php
    $colors = [
        'success' => 'bg-success-subtle text-success border border-success-subtle',
        'warning' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        'danger'  => 'bg-danger-subtle text-danger border border-danger-subtle',
        'info'    => 'bg-info-subtle text-info-emphasis border border-info-subtle',
        'default' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
        'primary' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
    ];
    $sizes = [
        'sm' => 'small px-2 py-0',
        'md' => 'small px-2 py-1',
        'lg' => 'px-3 py-2',
    ];
    $dotBg = [
        'success' => 'bg-success', 'warning' => 'bg-warning', 'danger' => 'bg-danger',
        'info' => 'bg-info', 'default' => 'bg-secondary', 'primary' => 'bg-primary',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'badge rounded-pill d-inline-flex align-items-center gap-1 fw-medium ' . $colors[$type] . ' ' . $sizes[$size]]) }}>
    @if($dot)
        <span class="d-inline-block rounded-circle {{ $dotBg[$type] }} {{ $pulse ? 'placeholder-glow' : '' }}" style="width:6px;height:6px"></span>
    @endif
    {{ $slot }}
</span>
