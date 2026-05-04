@props([
    'title' => 'Nessun elemento',
    'description' => 'Non ci sono elementi da visualizzare.',
    'icon' => null,
    'actionText' => null,
    'actionUrl' => null,
])

<div {{ $attributes->merge(['class' => 'text-center py-5 px-3']) }}>
    <div class="mx-auto bg-light rounded-4 d-inline-flex align-items-center justify-content-center mb-4" style="width:64px;height:64px">
        @if($icon)
            {!! $icon !!}
        @else
            <i class="bi bi-inbox text-muted fs-2"></i>
        @endif
    </div>

    <h3 class="h5 fw-semibold text-dark mb-2">{{ $title }}</h3>
    <p class="text-muted mx-auto mb-4" style="max-width:24rem">{{ $description }}</p>

    @if($actionText && $actionUrl)
        <a href="{{ $actionUrl }}" class="btn btn-primary rounded-3 d-inline-flex align-items-center">
            <i class="bi bi-plus-lg me-2"></i>{{ $actionText }}
        </a>
    @endif

    {{ $slot }}
</div>
