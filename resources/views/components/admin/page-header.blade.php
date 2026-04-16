@props([
    'title',
    'subtitle' => null,
    'backUrl' => null,
])

<div {{ $attributes->merge(['class' => 'mb-8']) }}>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            @if($backUrl)
                <a href="{{ $backUrl }}" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
            @endif
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $title }}</h1>
                @if($subtitle)
                    <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        
        @if(isset($actions))
            <div class="flex items-center gap-3">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
