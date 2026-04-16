@props([
    'title',
    'value',
    'change' => null,
    'changeType' => 'neutral', // positive, negative, neutral
    'icon' => null,
    'iconBg' => 'bg-primary-100',
    'iconColor' => 'text-primary-600',
    'href' => null,
])

@php
    $changeColors = [
        'positive' => 'text-emerald-600 bg-emerald-50',
        'negative' => 'text-red-600 bg-red-50',
        'neutral' => 'text-gray-600 bg-gray-100',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-sm border border-gray-100 p-6 card-hover stat-card']) }}>
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">{{ $title }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $value }}</p>
            
            @if($change !== null)
                <div class="mt-3 flex items-center gap-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium {{ $changeColors[$changeType] }}">
                        @if($changeType === 'positive')
                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                        @elseif($changeType === 'negative')
                            <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        @endif
                        {{ $change }}
                    </span>
                    <span class="text-xs text-gray-500">vs mese scorso</span>
                </div>
            @endif
        </div>
        
        @if($icon)
            <div class="flex-shrink-0 w-12 h-12 {{ $iconBg }} rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 {{ $iconColor }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    {!! $icon !!}
                </svg>
            </div>
        @endif
    </div>
    
    @if($href)
        <a href="{{ $href }}" class="mt-4 inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-700 group">
            Vedi dettagli
            <svg class="w-4 h-4 ml-1 transform transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    @endif
</div>
