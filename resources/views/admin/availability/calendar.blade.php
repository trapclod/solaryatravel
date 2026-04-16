@extends('layouts.admin')

@section('title', 'Disponibilità - ' . $catamaran->name)

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.availability.index') }}" 
                   class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $catamaran->name }}</h1>
                    <p class="text-gray-600">Gestisci disponibilità e blocchi</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" 
                        onclick="document.getElementById('bulk-modal').classList.remove('hidden')"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Modifica in Blocco
                </button>
            </div>
        </div>

        {{-- Legend --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex flex-wrap items-center gap-6">
                <span class="text-sm font-medium text-gray-700">Legenda:</span>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 bg-green-500 rounded"></span>
                    <span class="text-sm text-gray-600">Disponibile</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 bg-blue-500 rounded"></span>
                    <span class="text-sm text-gray-600">Prenotato</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 bg-yellow-500 rounded"></span>
                    <span class="text-sm text-gray-600">Completo</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 bg-red-500 rounded"></span>
                    <span class="text-sm text-gray-600">Bloccato</span>
                </div>
            </div>
        </div>

        {{-- Calendar --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div id="calendar"></div>
        </div>
    </div>

    {{-- Block Date Modal --}}
    <div id="block-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('block-modal').classList.add('hidden')"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Blocca/Sblocca Data</h3>
                <form id="block-form" method="POST">
                    @csrf
                    <input type="hidden" name="date" id="block-date">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data Selezionata</label>
                            <p id="block-date-display" class="text-gray-900 font-medium"></p>
                        </div>

                        <div>
                            <label for="block-time-slot" class="block text-sm font-medium text-gray-700 mb-1">Fascia Oraria</label>
                            <select name="time_slot_id" id="block-time-slot" 
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Tutte le fasce</option>
                                @foreach($timeSlots as $slot)
                                    <option value="{{ $slot->id }}">{{ $slot->name }} ({{ $slot->start_time }} - {{ $slot->end_time }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="block-reason" class="block text-sm font-medium text-gray-700 mb-1">Motivo (opzionale)</label>
                            <input type="text" name="block_reason" id="block-reason" 
                                   placeholder="es. Manutenzione, Evento privato..."
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-6">
                        <button type="button" 
                                onclick="document.getElementById('block-modal').classList.add('hidden')"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Annulla
                        </button>
                        <button type="button" onclick="unblockDate()"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            Sblocca
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Blocca
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Bulk Update Modal --}}
    <div id="bulk-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('bulk-modal').classList.add('hidden')"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Modifica in Blocco</h3>
                <form action="{{ route('admin.availability.bulk', $catamaran) }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="bulk-date-from" class="block text-sm font-medium text-gray-700 mb-1">Data Inizio</label>
                                <input type="date" name="date_from" id="bulk-date-from" required
                                       min="{{ now()->format('Y-m-d') }}"
                                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label for="bulk-date-to" class="block text-sm font-medium text-gray-700 mb-1">Data Fine</label>
                                <input type="date" name="date_to" id="bulk-date-to" required
                                       min="{{ now()->format('Y-m-d') }}"
                                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>

                        <div>
                            <label for="bulk-time-slot" class="block text-sm font-medium text-gray-700 mb-1">Fascia Oraria</label>
                            <select name="time_slot_id" id="bulk-time-slot" 
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Tutte le fasce</option>
                                @foreach($timeSlots as $slot)
                                    <option value="{{ $slot->id }}">{{ $slot->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Giorni della Settimana</label>
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $days = ['Dom', 'Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab'];
                                @endphp
                                @foreach($days as $index => $day)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="days_of_week[]" value="{{ $index }}" checked
                                               class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                        <span class="ml-1 text-sm text-gray-700">{{ $day }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label for="bulk-action" class="block text-sm font-medium text-gray-700 mb-1">Azione</label>
                            <select name="action" id="bulk-action" required
                                    onchange="toggleSeatsField(this.value)"
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="block">Blocca date</option>
                                <option value="unblock">Sblocca date</option>
                                <option value="set_seats">Imposta posti disponibili</option>
                            </select>
                        </div>

                        <div id="seats-field" class="hidden">
                            <label for="bulk-seats" class="block text-sm font-medium text-gray-700 mb-1">Posti Disponibili</label>
                            <input type="number" name="seats_available" id="bulk-seats" 
                                   min="0" max="{{ $catamaran->capacity }}" value="{{ $catamaran->capacity }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>

                        <div>
                            <label for="bulk-reason" class="block text-sm font-medium text-gray-700 mb-1">Motivo (per blocchi)</label>
                            <input type="text" name="block_reason" id="bulk-reason" 
                                   placeholder="es. Stagione chiusa"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-6">
                        <button type="button" 
                                onclick="document.getElementById('bulk-modal').classList.add('hidden')"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            Annulla
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                            Applica
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'it',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek'
            },
            events: [
                @foreach($availability as $date => $slots)
                    @foreach($slots as $slot)
                    {
                        id: 'avail-{{ $slot->id }}',
                        title: '{{ $slot->status === "blocked" ? "Bloccato" : "Posti: " . $slot->seats_available }}',
                        start: '{{ $date }}',
                        color: '{{ $slot->status === "blocked" ? "#EF4444" : ($slot->status === "fully_booked" ? "#F59E0B" : "#10B981") }}',
                        extendedProps: {
                            type: 'availability',
                            status: '{{ $slot->status }}',
                            timeSlot: '{{ $slot->timeSlot?->name }}'
                        }
                    },
                    @endforeach
                @endforeach
                @foreach($bookings as $date => $dateBookings)
                    @foreach($dateBookings as $booking)
                    {
                        id: 'booking-{{ $booking->id }}',
                        title: '#{{ $booking->booking_number }} - {{ $booking->seats }} posti',
                        start: '{{ $date }}',
                        color: '#3B82F6',
                        extendedProps: {
                            type: 'booking',
                            bookingNumber: '{{ $booking->booking_number }}',
                            customer: '{{ $booking->customer_first_name }} {{ $booking->customer_last_name }}',
                            seats: {{ $booking->seats }}
                        }
                    },
                    @endforeach
                @endforeach
            ],
            dateClick: function(info) {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const clickedDate = new Date(info.dateStr);
                
                if (clickedDate >= today) {
                    openBlockModal(info.dateStr);
                }
            },
            eventClick: function(info) {
                if (info.event.extendedProps.type === 'booking') {
                    window.location.href = '{{ route("admin.bookings.index") }}?search=' + info.event.extendedProps.bookingNumber;
                } else {
                    openBlockModal(info.event.startStr);
                }
            }
        });
        calendar.render();

        window.openBlockModal = function(dateStr) {
            const modal = document.getElementById('block-modal');
            const dateInput = document.getElementById('block-date');
            const dateDisplay = document.getElementById('block-date-display');
            
            dateInput.value = dateStr;
            dateDisplay.textContent = new Date(dateStr).toLocaleDateString('it-IT', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            document.getElementById('block-form').action = '{{ route("admin.availability.block", $catamaran) }}';
            modal.classList.remove('hidden');
        };

        window.unblockDate = function() {
            const form = document.getElementById('block-form');
            form.action = '{{ route("admin.availability.unblock", $catamaran) }}';
            form.submit();
        };

        window.toggleSeatsField = function(action) {
            const seatsField = document.getElementById('seats-field');
            if (action === 'set_seats') {
                seatsField.classList.remove('hidden');
            } else {
                seatsField.classList.add('hidden');
            }
        };
    });
</script>
@endpush

@push('styles')
<style>
    .fc-day-past {
        background-color: #f9fafb;
    }
    .fc-day-today {
        background-color: #eff6ff !important;
    }
    .fc-event {
        cursor: pointer;
        font-size: 0.75rem;
        padding: 2px 4px;
    }
</style>
@endpush
