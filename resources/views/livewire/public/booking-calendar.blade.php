<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    {{-- Calendar Header --}}
    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b">
        <button 
            wire:click="previousMonth"
            class="p-2 rounded-lg hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            @disabled(\Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->lte(now()->startOfMonth()))
        >
            <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        
        <h3 class="text-lg font-semibold text-navy-900 capitalize">{{ $monthName }}</h3>
        
        <button 
            wire:click="nextMonth"
            class="p-2 rounded-lg hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            @disabled(\Carbon\Carbon::createFromDate($currentYear, $currentMonth, 1)->gte(now()->addMonths(6)->startOfMonth()))
        >
            <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>
    
    {{-- Week Days Header --}}
    <div class="grid grid-cols-7 gap-px bg-gray-200">
        @foreach($weekDays as $day)
            <div class="bg-gray-50 px-2 py-2 text-center">
                <span class="text-xs font-medium text-gray-500 uppercase">{{ $day }}</span>
            </div>
        @endforeach
    </div>
    
    {{-- Calendar Days --}}
    <div class="grid grid-cols-7 gap-px bg-gray-200">
        @foreach($days as $day)
            <div @class([
                'bg-white',
                'opacity-40' => !$day['isCurrentMonth'],
            ])>
                @if($day['isCurrentMonth'])
                    @if($day['isAvailable'] && !$day['isPast'])
                        <button 
                            wire:click="selectDate('{{ $day['date'] }}')"
                            @class([
                                'w-full h-12 sm:h-14 flex items-center justify-center text-sm font-medium transition-all duration-200 relative',
                                'bg-gold-500 text-white hover:bg-gold-600' => $day['isSelected'],
                                'bg-primary-50 text-primary-700 hover:bg-primary-100' => !$day['isSelected'] && $day['isAvailable'],
                            ])
                        >
                            {{ $day['day'] }}
                            @if($day['isToday'])
                                <span class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 rounded-full bg-current"></span>
                            @endif
                        </button>
                    @else
                        <div @class([
                            'w-full h-12 sm:h-14 flex items-center justify-center text-sm font-medium',
                            'text-gray-300' => $day['isPast'] || !$day['isAvailable'],
                            'bg-gray-50' => !$day['isAvailable'] && !$day['isPast'],
                        ])>
                            {{ $day['day'] }}
                            @if($day['isToday'])
                                <span class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-1 h-1 rounded-full bg-primary-500"></span>
                            @endif
                        </div>
                    @endif
                @else
                    <div class="w-full h-12 sm:h-14 flex items-center justify-center text-sm text-gray-300">
                        {{ $day['day'] }}
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    
    {{-- Legend --}}
    <div class="flex items-center justify-center gap-6 px-4 py-3 border-t bg-gray-50">
        <div class="flex items-center gap-2 text-xs text-gray-600">
            <span class="w-4 h-4 rounded bg-primary-50 border border-primary-200"></span>
            <span>Disponibile</span>
        </div>
        <div class="flex items-center gap-2 text-xs text-gray-600">
            <span class="w-4 h-4 rounded bg-gold-500"></span>
            <span>Selezionato</span>
        </div>
        <div class="flex items-center gap-2 text-xs text-gray-600">
            <span class="w-4 h-4 rounded bg-gray-100 border border-gray-200"></span>
            <span>Non disponibile</span>
        </div>
    </div>
</div>
