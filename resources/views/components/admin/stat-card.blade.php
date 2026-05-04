@props([
    'title',
    'value',
    'change' => null,
    'changeType' => 'neutral',
    'icon' => null,
    'iconBg' => 'bg-primary-subtle',
    'iconColor' => 'text-primary',
    'href' => null,
])

@php
    $changeColors = [
        'positive' => 'bg-success-subtle text-success',
        'negative' => 'bg-danger-subtle text-danger',
        'neutral'  => 'bg-secondary-subtle text-secondary',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'stat-card card-hover']) }}>
    <div class="d-flex align-items-start justify-content-between">
        <div class="flex-grow-1">
            <p class="small text-uppercase text-muted fw-medium m-0" style="letter-spacing:.05em">{{ $title }}</p>
            <p class="h3 fw-bold text-dark mt-2 mb-0">{{ $value }}</p>

            @if($change !== null)
                <div class="d-flex align-items-center gap-2 mt-3">
                    <span class="badge rounded-pill px-2 py-1 small d-inline-flex align-items-center {{ $changeColors[$changeType] }}">
                        @if($changeType === 'positive')<i class="bi bi-arrow-up me-1"></i>
                        @elseif($changeType === 'negative')<i class="bi bi-arrow-down me-1"></i>
                        @endif
                        {{ $change }}
                    </span>
                    <small class="text-muted">vs mese scorso</small>
                </div>
            @endif
        </div>

        @if($icon)
            <div class="flex-shrink-0 {{ $iconBg }} rounded-3 d-inline-flex align-items-center justify-content-center" style="width:48px;height:48px">
                <span class="{{ $iconColor }}">{!! $icon !!}</span>
            </div>
        @endif
    </div>

    @if($href)
        <a href="{{ $href }}" class="d-inline-flex align-items-center mt-3 small fw-medium text-primary text-decoration-none">
            Vedi dettagli<i class="bi bi-arrow-right ms-1"></i>
        </a>
    @endif
</div>
