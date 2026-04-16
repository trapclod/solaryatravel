@props([
    'title' => null,
    'subtitle' => null,
    'padding' => true,
    'hover' => false,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden' . ($hover ? ' card-hover' : '')]) }}>
    @if($title || isset($header))
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                @if($title)
                    <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-sm text-gray-500 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @if(isset($header))
                <div>{{ $header }}</div>
            @endif
        </div>
    @endif
    
    <div class="{{ $padding ? 'p-6' : '' }}">
        {{ $slot }}
    </div>
    
    @if(isset($footer))
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $footer }}
        </div>
    @endif
</div>
