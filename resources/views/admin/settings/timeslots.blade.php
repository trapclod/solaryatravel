@extends('layouts.admin')

@section('title', 'Fasce orarie')

@section('content')
    {{-- Page header --}}
    <div class="dash-page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.settings') }}" class="dash-icon-btn" title="Torna alle impostazioni">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="mb-0">Fasce orarie</h1>
                <p class="mt-1 mb-0">
                    <i class="bi bi-clock-history me-1"></i>Gestisci gli slot di prenotazione disponibili
                </p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" onclick="addTimeSlot()" class="btn btn-light border rounded-pill px-3 fw-semibold">
                <i class="bi bi-plus-lg me-1"></i>Aggiungi fascia
            </button>
            <button type="submit" form="timeSlotsForm" class="btn btn-primary rounded-pill px-3 fw-semibold">
                <i class="bi bi-check2-circle me-1"></i>Salva fasce
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-3 d-flex align-items-center gap-2 mb-3"
             style="background:rgba(16,185,129,.1); color:#059669">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Helper card --}}
    <div class="alert border-0 rounded-3 d-flex gap-2 mb-3"
         style="background:rgba(2,132,199,.08); color:#0369a1">
        <i class="bi bi-info-circle-fill fs-5 flex-shrink-0"></i>
        <div class="small">
            <strong>Modificatore prezzo</strong> moltiplica il prezzo base del catamarano (1.00 = nessuna variazione, 1.5 = +50%, 0.8 = -20%).
            L'<strong>ordine</strong> determina la posizione di visualizzazione pubblica.
        </div>
    </div>

    <form action="{{ route('admin.settings.timeslots.update') }}" method="POST" id="timeSlotsForm">
        @csrf

        <div class="dash-card mb-4">
            <div class="dash-card-header">
                <h3><i class="bi bi-clock me-2 text-primary"></i>Fasce orarie configurate</h3>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                    <span id="slotsCount">{{ $timeSlots->count() }}</span> fasce
                </span>
            </div>

            <div class="table-responsive">
                <table class="dash-table mb-0" id="timeSlotsTable">
                    <thead>
                        <tr>
                            <th style="width:80px">Ordine</th>
                            <th>Nome</th>
                            <th style="width:130px">Inizio</th>
                            <th style="width:130px">Fine</th>
                            <th style="width:170px">Tipo</th>
                            <th style="width:140px">Modif. prezzo</th>
                            <th class="text-center" style="width:100px">Attivo</th>
                            <th class="text-center" style="width:80px">Azioni</th>
                        </tr>
                    </thead>
                    <tbody id="timeSlotsBody">
                        @foreach($timeSlots as $index => $slot)
                            <tr class="time-slot-row">
                                <td>
                                    <input type="hidden" name="slots[{{ $index }}][id]" value="{{ $slot->id }}">
                                    <input type="number" name="slots[{{ $index }}][sort_order]"
                                           value="{{ $slot->sort_order }}"
                                           class="form-control form-control-sm text-center" min="0" required>
                                </td>
                                <td>
                                    <input type="text" name="slots[{{ $index }}][name]"
                                           value="{{ $slot->name }}"
                                           class="form-control form-control-sm fw-semibold"
                                           placeholder="Es. Mattina" required>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="bi bi-sunrise"></i></span>
                                        <input type="time" name="slots[{{ $index }}][start_time]"
                                               value="{{ $slot->start_time ? $slot->start_time->format('H:i') : '' }}"
                                               class="form-control form-control-sm" required>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text"><i class="bi bi-sunset"></i></span>
                                        <input type="time" name="slots[{{ $index }}][end_time]"
                                               value="{{ $slot->end_time ? $slot->end_time->format('H:i') : '' }}"
                                               class="form-control form-control-sm" required>
                                    </div>
                                </td>
                                <td>
                                    <select name="slots[{{ $index }}][slot_type]" class="form-select form-select-sm">
                                        <option value="half_day" {{ $slot->slot_type === 'half_day' ? 'selected' : '' }}>Mezza giornata</option>
                                        <option value="full_day" {{ $slot->slot_type === 'full_day' ? 'selected' : '' }}>Giornata intera</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="slots[{{ $index }}][price_modifier]"
                                               value="{{ $slot->price_modifier }}"
                                               class="form-control form-control-sm text-end fw-semibold"
                                               step="0.01" min="0" max="10" required>
                                        <span class="input-group-text">×</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block m-0">
                                        <input type="checkbox" name="slots[{{ $index }}][is_active]" value="1"
                                               class="form-check-input" role="switch"
                                               {{ $slot->is_active ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button type="button" onclick="removeTimeSlot(this)"
                                            class="dash-icon-btn text-danger" title="Rimuovi">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($timeSlots->isEmpty())
                <div id="emptyState" class="text-center py-5 text-muted">
                    <i class="bi bi-clock fs-1 d-block mb-2 opacity-50"></i>
                    <p class="fw-semibold mb-1">Nessuna fascia oraria configurata</p>
                    <p class="small mb-3">Clicca "Aggiungi fascia" per iniziare</p>
                    <button type="button" onclick="addTimeSlot()" class="btn btn-primary rounded-pill px-3 fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i>Aggiungi prima fascia
                    </button>
                </div>
            @endif
        </div>
    </form>

    <template id="timeSlotTemplate">
        <tr class="time-slot-row">
            <td>
                <input type="hidden" name="slots[INDEX][id]" value="">
                <input type="number" name="slots[INDEX][sort_order]" value="0"
                       class="form-control form-control-sm text-center" min="0" required>
            </td>
            <td>
                <input type="text" name="slots[INDEX][name]" value=""
                       class="form-control form-control-sm fw-semibold"
                       placeholder="Es. Mattina" required>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-sunrise"></i></span>
                    <input type="time" name="slots[INDEX][start_time]" value="09:00"
                           class="form-control form-control-sm" required>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-sunset"></i></span>
                    <input type="time" name="slots[INDEX][end_time]" value="13:00"
                           class="form-control form-control-sm" required>
                </div>
            </td>
            <td>
                <select name="slots[INDEX][slot_type]" class="form-select form-select-sm">
                    <option value="half_day">Mezza giornata</option>
                    <option value="full_day">Giornata intera</option>
                </select>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" name="slots[INDEX][price_modifier]" value="1.00"
                           class="form-control form-control-sm text-end fw-semibold"
                           step="0.01" min="0" max="10" required>
                    <span class="input-group-text">×</span>
                </div>
            </td>
            <td class="text-center">
                <div class="form-check form-switch d-inline-block m-0">
                    <input type="checkbox" name="slots[INDEX][is_active]" value="1" checked
                           class="form-check-input" role="switch">
                </div>
            </td>
            <td class="text-center">
                <button type="button" onclick="removeTimeSlot(this)"
                        class="dash-icon-btn text-danger" title="Rimuovi">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    </template>
@endsection

@push('scripts')
<script>
    let slotIndex = {{ $timeSlots->count() }};

    function updateCount() {
        document.getElementById('slotsCount').textContent =
            document.querySelectorAll('#timeSlotsBody .time-slot-row').length;
    }

    function addTimeSlot() {
        const template = document.getElementById('timeSlotTemplate');
        const tbody = document.getElementById('timeSlotsBody');
        const empty = document.getElementById('emptyState');
        if (empty) empty.remove();

        const html = template.content.firstElementChild.outerHTML.replace(/INDEX/g, slotIndex);
        tbody.insertAdjacentHTML('beforeend', html);
        slotIndex++;
        updateCount();
    }

    function removeTimeSlot(button) {
        if (!confirm('Sei sicuro di voler rimuovere questa fascia oraria?')) return;
        button.closest('tr').remove();
        reindexSlots();
        updateCount();
    }

    function reindexSlots() {
        document.querySelectorAll('#timeSlotsBody .time-slot-row').forEach((row, index) => {
            row.querySelectorAll('[name]').forEach(input => {
                input.name = input.name.replace(/slots\[\d+\]/, `slots[${index}]`);
            });
        });
        slotIndex = document.querySelectorAll('#timeSlotsBody .time-slot-row').length;
    }
</script>
@endpush
