@props([
    'title' => null,
    'subtitle' => null,
    'padding' => true,
    'hover' => false,
])

<div {{ $attributes->merge(['class' => 'card border-0 shadow-sm rounded-4 overflow-hidden' . ($hover ? ' card-hover' : '')]) }}>
    @if($title || isset($header))
        <div class="card-header bg-white border-bottom d-flex align-items-center justify-content-between py-3 px-4">
            <div>
                @if($title)<h3 class="h6 fw-semibold text-dark m-0">{{ $title }}</h3>@endif
                @if($subtitle)<p class="small text-muted m-0 mt-1">{{ $subtitle }}</p>@endif
            </div>
            @if(isset($header))<div>{{ $header }}</div>@endif
        </div>
    @endif

    <div class="{{ $padding ? 'card-body p-4' : 'card-body p-0' }}">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="card-footer bg-light border-top px-4 py-3">{{ $footer }}</div>
    @endif
</div>
