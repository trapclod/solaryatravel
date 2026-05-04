@props([
    'title',
    'subtitle' => null,
    'backUrl' => null,
])

<div {{ $attributes->merge(['class' => 'mb-4 mb-lg-5']) }}>
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-3">
            @if($backUrl)
                <a href="{{ $backUrl }}" class="btn btn-light btn-sm rounded-3 d-inline-flex align-items-center justify-content-center" style="width:40px;height:40px" aria-label="Indietro">
                    <i class="bi bi-arrow-left"></i>
                </a>
            @endif
            <div>
                <h1 class="h3 fw-bold text-dark mb-0">{{ $title }}</h1>
                @if($subtitle)<p class="small text-muted mb-0 mt-1">{{ $subtitle }}</p>@endif
            </div>
        </div>

        @if(isset($actions))
            <div class="d-flex align-items-center gap-2">{{ $actions }}</div>
        @endif
    </div>
</div>
