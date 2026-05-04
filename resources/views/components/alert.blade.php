@props([
    'type' => 'info',
    'dismissible' => true,
])

@php
    $map = [
        'info'    => ['class' => 'alert-info',    'icon' => 'bi-info-circle-fill'],
        'success' => ['class' => 'alert-success', 'icon' => 'bi-check-circle-fill'],
        'warning' => ['class' => 'alert-warning', 'icon' => 'bi-exclamation-triangle-fill'],
        'error'   => ['class' => 'alert-danger',  'icon' => 'bi-x-circle-fill'],
    ];
    $config = $map[$type] ?? $map['info'];
@endphp

<div {{ $attributes->merge(['class' => 'alert ' . $config['class'] . ($dismissible ? ' alert-dismissible' : '') . ' d-flex align-items-start']) }} role="alert">
    <i class="bi {{ $config['icon'] }} fs-5 me-2 flex-shrink-0"></i>
    <div class="flex-grow-1">{{ $slot }}</div>
    @if($dismissible)
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
    @endif
</div>
