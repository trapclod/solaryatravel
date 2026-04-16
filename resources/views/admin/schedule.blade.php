@extends('layouts.admin')

@section('title', 'Programma')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Programma</h1>
                <p class="text-gray-600">Calendario delle prenotazioni</p>
            </div>
            <div class="flex items-center gap-4">
                <select id="catamaran-filter" class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Tutti i catamarani</option>
                    @foreach($catamarans as $catamaran)
                        <option value="{{ $catamaran->id }}">{{ $catamaran->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Legend --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="flex flex-wrap items-center gap-6">
                <span class="text-sm font-medium text-gray-700">Legenda:</span>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                    <span class="text-sm text-gray-600">In attesa</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="text-sm text-gray-600">Confermata</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <span class="text-sm text-gray-600">Completata</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="text-sm text-gray-600">Annullata</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-gray-500"></span>
                    <span class="text-sm text-gray-600">No show</span>
                </div>
            </div>
        </div>

        {{-- Calendar --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div id="calendar"></div>
        </div>

        {{-- Booking Details Modal --}}
        <div id="booking-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Dettagli Prenotazione</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div id="modal-content" class="space-y-4">
                    <!-- Content will be injected here -->
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const bookings = @json($bookings);
            
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'it',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                buttonText: {
                    today: 'Oggi',
                    month: 'Mese',
                    week: 'Settimana',
                    day: 'Giorno',
                    list: 'Lista'
                },
                events: bookings,
                eventClick: function(info) {
                    showBookingDetails(info.event);
                },
                eventDidMount: function(info) {
                    info.el.style.cursor = 'pointer';
                }
            });
            
            calendar.render();

            // Filter by catamaran
            document.getElementById('catamaran-filter').addEventListener('change', function() {
                const catamaranId = this.value;
                calendar.getEvents().forEach(event => {
                    if (catamaranId === '' || event.extendedProps.catamaran_id == catamaranId) {
                        event.setProp('display', 'auto');
                    } else {
                        event.setProp('display', 'none');
                    }
                });
            });
        });

        function showBookingDetails(event) {
            const modal = document.getElementById('booking-modal');
            const content = document.getElementById('modal-content');
            
            content.innerHTML = `
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Numero:</span>
                        <span class="font-semibold">#${event.extendedProps.booking_number}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Cliente:</span>
                        <span class="font-semibold">${event.title}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Catamarano:</span>
                        <span class="font-semibold">${event.extendedProps.catamaran}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ospiti:</span>
                        <span class="font-semibold">${event.extendedProps.guests}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Data:</span>
                        <span class="font-semibold">${event.start.toLocaleDateString('it-IT')}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Orario:</span>
                        <span class="font-semibold">${event.start.toLocaleTimeString('it-IT', {hour: '2-digit', minute: '2-digit'})} - ${event.end ? event.end.toLocaleTimeString('it-IT', {hour: '2-digit', minute: '2-digit'}) : 'N/A'}</span>
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <a href="/admin/bookings/${event.id}" class="flex-1 px-4 py-2 bg-primary-600 text-white text-center text-sm font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                        Visualizza Dettagli
                    </a>
                </div>
            `;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('booking-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal on backdrop click
        document.getElementById('booking-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
    @endpush
