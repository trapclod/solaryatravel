@extends('layouts.admin')

@section('title', 'Disponibilità - ' . $catamaran->name)

@section('content')
    {{-- Page header --}}
    <div class="dash-page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.availability.index') }}" class="dash-icon-btn" title="Torna all'elenco">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1>Disponibilità <span class="text-primary">{{ $catamaran->name }}</span></h1>
                <p>Gestisci blocchi giornalieri, posti disponibili e modifiche in blocco.</p>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.catamarans.show', $catamaran) }}" class="btn btn-light rounded-pill px-3 fw-semibold border">
                <i class="bi bi-eye me-2"></i>Dettagli catamarano
            </a>
            <button type="button" class="btn btn-primary rounded-pill px-3 fw-semibold"
                    data-bs-toggle="modal" data-bs-target="#bulk-modal">
                <i class="bi bi-arrow-repeat me-2"></i>Modifica in blocco
            </button>
        </div>
    </div>

    {{-- Mini stats --}}
    @php
        $totalSlots = collect($availability)->flatten()->count();
        $blockedSlots = collect($availability)->flatten()->where('status', 'blocked')->count();
        $fullySlots = collect($availability)->flatten()->where('status', 'fully_booked')->count();
        $totalBookings = collect($bookings)->flatten()->count();
    @endphp
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat is-active">
                <div class="dash-mini-stat-label"><i class="bi bi-calendar-check me-1"></i>Slot configurati</div>
                <div class="dash-mini-stat-value">{{ $totalSlots }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-journal-bookmark me-1"></i>Prenotazioni</div>
                <div class="dash-mini-stat-value text-primary">{{ $totalBookings }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-people-fill me-1"></i>Completi</div>
                <div class="dash-mini-stat-value text-warning">{{ $fullySlots }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-slash-circle me-1"></i>Bloccati</div>
                <div class="dash-mini-stat-value text-danger">{{ $blockedSlots }}</div>
            </div>
        </div>
    </div>

    {{-- Legend --}}
    <div class="dash-card mb-3">
        <div class="dash-card-body py-3">
            <div class="cal-legend">
                <span class="small fw-semibold text-muted me-2">Legenda:</span>
                <div class="cal-legend-item">
                    <span class="cal-legend-dot" style="background:#10b981"></span>Disponibile
                </div>
                <div class="cal-legend-item">
                    <span class="cal-legend-dot" style="background:#3b82f6"></span>Prenotato
                </div>
                <div class="cal-legend-item">
                    <span class="cal-legend-dot" style="background:#f59e0b"></span>Completo
                </div>
                <div class="cal-legend-item">
                    <span class="cal-legend-dot" style="background:#ef4444"></span>Bloccato
                </div>
                <span class="ms-auto small text-muted d-none d-md-inline">
                    <i class="bi bi-lightbulb me-1 text-warning"></i>
                    Clicca su una data per bloccarla o sbloccarla.
                </span>
            </div>
        </div>
    </div>

    {{-- Calendar --}}
    <div class="dash-card mb-3">
        <div class="dash-card-body">
            <div id="calendar" class="dash-calendar"></div>
        </div>
    </div>

    {{-- Block modal --}}
    <div class="modal fade" id="block-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:1rem">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-slash-circle text-danger me-2"></i>Blocca / Sblocca data
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="block-form" method="POST">
                    @csrf
                    <input type="hidden" name="date" id="block-date">

                    <div class="modal-body">
                        <div class="alert alert-info bg-info-subtle border-0 small d-flex align-items-center" style="border-radius:.65rem">
                            <i class="bi bi-calendar-event me-2 fs-5"></i>
                            <span><strong>Data:</strong> <span id="block-date-display"></span></span>
                        </div>

                        <div class="mb-3">
                            <label for="block-time-slot" class="form-label fw-semibold small">Fascia oraria</label>
                            <select name="time_slot_id" id="block-time-slot" class="form-select">
                                <option value="">Tutte le fasce</option>
                                @foreach($timeSlots as $slot)
                                    <option value="{{ $slot->id }}">
                                        {{ $slot->name }}
                                        ({{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="block-reason" class="form-label fw-semibold small">Motivo <span class="text-muted fw-normal">(opzionale)</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-chat-left-text"></i></span>
                                <input type="text" name="block_reason" id="block-reason"
                                       placeholder="es. Manutenzione, Evento privato..."
                                       class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 d-flex justify-content-between gap-2">
                        <button type="button" onclick="unblockDate()" class="btn btn-outline-success rounded-pill px-3 fw-semibold">
                            <i class="bi bi-unlock me-2"></i>Sblocca
                        </button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light border rounded-pill px-3 fw-semibold" data-bs-dismiss="modal">Annulla</button>
                            <button type="submit" class="btn btn-danger rounded-pill px-3 fw-semibold">
                                <i class="bi bi-lock me-2"></i>Blocca
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Bulk modal --}}
    <div class="modal fade" id="bulk-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius:1rem">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-arrow-repeat text-primary me-2"></i>Modifica in blocco
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.availability.bulk', $catamaran) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="bulk-date-from" class="form-label fw-semibold small">Data inizio</label>
                                <input type="date" name="date_from" id="bulk-date-from" required
                                       min="{{ now()->format('Y-m-d') }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="bulk-date-to" class="form-label fw-semibold small">Data fine</label>
                                <input type="date" name="date_to" id="bulk-date-to" required
                                       min="{{ now()->format('Y-m-d') }}" class="form-control">
                            </div>

                            <div class="col-12">
                                <label for="bulk-time-slot" class="form-label fw-semibold small">Fascia oraria</label>
                                <select name="time_slot_id" id="bulk-time-slot" class="form-select">
                                    <option value="">Tutte le fasce</option>
                                    @foreach($timeSlots as $slot)
                                        <option value="{{ $slot->id }}">{{ $slot->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Giorni della settimana</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @php $days = ['Dom','Lun','Mar','Mer','Gio','Ven','Sab']; @endphp
                                    @foreach($days as $i => $d)
                                        <input type="checkbox" class="btn-check" name="days_of_week[]" value="{{ $i }}"
                                               id="dow-{{ $i }}" checked>
                                        <label for="dow-{{ $i }}" class="btn btn-outline-primary rounded-pill px-3 fw-semibold">{{ $d }}</label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="bulk-action" class="form-label fw-semibold small">Azione</label>
                                <select name="action" id="bulk-action" required class="form-select"
                                        onchange="toggleSeatsField(this.value)">
                                    <option value="block">🔒 Blocca date</option>
                                    <option value="unblock">🔓 Sblocca date</option>
                                    <option value="set_seats">👥 Imposta posti disponibili</option>
                                </select>
                            </div>
                            <div class="col-md-6 d-none" id="seats-field">
                                <label for="bulk-seats" class="form-label fw-semibold small">Posti disponibili</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-people"></i></span>
                                    <input type="number" name="seats_available" id="bulk-seats"
                                           min="0" max="{{ $catamaran->capacity }}" value="{{ $catamaran->capacity }}"
                                           class="form-control">
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="bulk-reason" class="form-label fw-semibold small">Motivo <span class="text-muted fw-normal">(per blocchi)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="bi bi-chat-left-text"></i></span>
                                    <input type="text" name="block_reason" id="bulk-reason"
                                           placeholder="es. Stagione chiusa" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light border rounded-pill px-3 fw-semibold" data-bs-dismiss="modal">Annulla</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-3 fw-semibold">
                            <i class="bi bi-check-lg me-2"></i>Applica
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
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'it',
            firstDay: 1,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek'
            },
            buttonText: { today: 'Oggi', month: 'Mese', week: 'Settimana' },
            dayMaxEvents: 3,
            events: [
                @foreach($availability as $date => $slots)
                    @foreach($slots as $slot)
                        {
                            id: 'avail-{{ $slot->id }}',
                            title: @json($slot->status === 'blocked' ? 'Bloccato' : 'Posti: ' . $slot->seats_available),
                            start: '{{ $date }}',
                            color: '{{ $slot->status === "blocked" ? "#ef4444" : ($slot->status === "fully_booked" ? "#f59e0b" : "#10b981") }}',
                            extendedProps: { type: 'availability', status: '{{ $slot->status }}', timeSlot: @json($slot->timeSlot?->name) }
                        },
                    @endforeach
                @endforeach
                @foreach($bookings as $date => $dateBookings)
                    @foreach($dateBookings as $booking)
                        {
                            id: 'booking-{{ $booking->id }}',
                            title: '#{{ $booking->booking_number }} · {{ $booking->seats }}p',
                            start: '{{ $date }}',
                            color: '#3b82f6',
                            extendedProps: { type: 'booking', bookingNumber: '{{ $booking->booking_number }}' }
                        },
                    @endforeach
                @endforeach
            ],
            dateClick: function (info) {
                const today = new Date(); today.setHours(0,0,0,0);
                const clicked = new Date(info.dateStr);
                if (clicked >= today) openBlockModal(info.dateStr);
            },
            eventClick: function (info) {
                if (info.event.extendedProps.type === 'booking') {
                    window.location.href = '{{ route("admin.bookings.index") }}?search=' + info.event.extendedProps.bookingNumber;
                } else {
                    openBlockModal(info.event.startStr);
                }
            }
        });
        calendar.render();

        const blockModalEl = document.getElementById('block-modal');
        const blockModal = new bootstrap.Modal(blockModalEl);

        window.openBlockModal = function (dateStr) {
            document.getElementById('block-date').value = dateStr;
            document.getElementById('block-date-display').textContent = new Date(dateStr).toLocaleDateString('it-IT', {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
            });
            document.getElementById('block-form').action = '{{ route("admin.availability.block", $catamaran) }}';
            blockModal.show();
        };

        window.unblockDate = function () {
            const form = document.getElementById('block-form');
            form.action = '{{ route("admin.availability.unblock", $catamaran) }}';
            form.submit();
        };

        window.toggleSeatsField = function (action) {
            document.getElementById('seats-field').classList.toggle('d-none', action !== 'set_seats');
        };
    });
</script>
@endpush
