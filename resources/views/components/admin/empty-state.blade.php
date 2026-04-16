@props([
    'title' => 'Nessun elemento',
    'description' => 'Non ci sono elementi da visualizzare.',
    'icon' => null,
    'actionText' => null,
    'actionUrl' => null,
])

<div {{ $attributes->merge(['class' => 'text-center py-12 px-6']) }}>
    <div class="mx-auto w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6">
        @if($icon)
            {!! $icon !!}
        @else
            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
        @endif
    </div>
    
    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $title }}</h3>
    <p class="text-gray-500 mb-6 max-w-sm mx-auto">{{ $description }}</p>
    
    @if($actionText && $actionUrl)
        <a href="{{ $actionUrl }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 rounded-xl transition-all shadow-sm hover:shadow-md">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            {{ $actionText }}
        </a>
    @endif
    
    {{ $slot }}
</div>
