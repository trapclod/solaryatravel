@php
    /** @var int $pi indice del periodo nel form */
    /** @var array $p dati del periodo: id?, label?, start_date?, end_date?, base_price?, weekdays?, times?, brackets[] */
    $brackets = $p['brackets'] ?? [];
    $pWeekdays = $p['weekdays'] ?? [1, 2, 3, 4, 5, 6, 7];
    if (!is_array($pWeekdays)) {
        $pWeekdays = json_decode($pWeekdays, true) ?: [1, 2, 3, 4, 5, 6, 7];
    }
    $pTimes = $p['times'] ?? ['10:00'];
    if (!is_array($pTimes)) {
        $pTimes = json_decode($pTimes, true) ?: ['10:00'];
    }
    $weekdayLabels = [1 => 'Lun', 2 => 'Mar', 3 => 'Mer', 4 => 'Gio', 5 => 'Ven', 6 => 'Sab', 7 => 'Dom'];
@endphp
<div class="period-card" data-period-index="{{ $pi }}">
    <div class="period-card-header">
        <div class="row g-2 align-items-end flex-grow-1">
            <div class="col-md-3">
                <label class="form-label fw-semibold small mb-1">Etichetta</label>
                <input type="hidden" name="periods[{{ $pi }}][id]" value="{{ $p['id'] ?? '' }}">
                <input type="text" name="periods[{{ $pi }}][label]" class="form-control form-control-sm"
                       value="{{ $p['label'] ?? '' }}" placeholder="es. Alta stagione">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small mb-1">Inizio <span class="text-danger">*</span></label>
                <input type="date" name="periods[{{ $pi }}][start_date]" class="form-control form-control-sm"
                       value="{{ $p['start_date'] ?? '' }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small mb-1">Fine <span class="text-danger">*</span></label>
                <input type="date" name="periods[{{ $pi }}][end_date]" class="form-control form-control-sm"
                       value="{{ $p['end_date'] ?? '' }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small mb-1">Prezzo adulto <span class="text-danger">*</span></label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light">€</span>
                    <input type="number" step="0.01" min="0" name="periods[{{ $pi }}][base_price]" class="form-control"
                           value="{{ $p['base_price'] ?? 0 }}" required>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-light border ms-2 align-self-end period-remove" title="Rimuovi periodo"
                onclick="removePeriod(this)">
            <i class="bi bi-trash text-danger"></i>
        </button>
    </div>

    <div class="period-card-body">
        <div class="row g-3 mb-3 pb-3 border-bottom">
            <div class="col-md-7">
                <div class="text-uppercase text-muted small fw-semibold mb-2">
                    <i class="bi bi-calendar-week me-1"></i>Giorni operativi
                </div>
                <div class="d-flex flex-wrap gap-2 weekday-chips" data-period-index="{{ $pi }}">
                    @foreach ($weekdayLabels as $d => $lbl)
                        @php $checked = in_array($d, $pWeekdays); @endphp
                        <label class="weekday-chip {{ $checked ? 'active' : '' }}">
                            <input type="checkbox" name="periods[{{ $pi }}][weekdays][]" value="{{ $d }}" @checked($checked) hidden>
                            <span>{{ $lbl }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="col-md-5">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="text-uppercase text-muted small fw-semibold">
                        <i class="bi bi-clock me-1"></i>Orari di partenza
                    </div>
                    <button type="button" class="btn btn-sm btn-light border rounded-pill fw-medium"
                            onclick="addTimeToPeriod({{ $pi }})">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>
                <div class="d-flex flex-wrap gap-2 period-times-wrap" data-period-index="{{ $pi }}">
                    @foreach ($pTimes as $ti => $t)
                        <div class="input-group input-group-sm period-time" style="width:auto">
                            <input type="time" name="periods[{{ $pi }}][times][]" class="form-control form-control-sm" value="{{ $t }}" required style="width:110px">
                            <button type="button" class="btn btn-sm btn-light border" onclick="this.closest('.period-time').remove()" title="Rimuovi">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
            <div class="text-uppercase text-muted small fw-semibold">
                <i class="bi bi-people me-1"></i>Fasce d'età
            </div>
            <button type="button" class="btn btn-sm btn-light border rounded-pill fw-medium"
                    onclick="addBracketToPeriod({{ $pi }})">
                <i class="bi bi-plus-lg me-1"></i>Aggiungi fascia
            </button>
        </div>

        <div class="table-responsive bracket-table-wrap">
            <table class="table align-middle bracket-table mb-0">
                <thead>
                    <tr>
                        <th style="width:25%">Etichetta <span class="text-danger">*</span></th>
                        <th style="width:13%">Età min</th>
                        <th style="width:13%">Età max</th>
                        <th style="width:18%">Prezzo (€) <span class="text-danger">*</span></th>
                        <th style="width:18%">Conta come posto</th>
                        <th style="width:13%" class="text-end"></th>
                    </tr>
                </thead>
                <tbody class="period-brackets-body" data-period-index="{{ $pi }}">
                    @foreach ($brackets as $bi => $b)
                        <tr>
                            <td>
                                <input type="hidden" name="periods[{{ $pi }}][brackets][{{ $bi }}][id]" value="{{ $b['id'] ?? '' }}">
                                <input type="text" name="periods[{{ $pi }}][brackets][{{ $bi }}][label]" class="form-control form-control-sm"
                                       value="{{ $b['label'] ?? '' }}" placeholder="es. Adulto">
                            </td>
                            <td><input type="number" min="0" max="120" name="periods[{{ $pi }}][brackets][{{ $bi }}][min_age]" class="form-control form-control-sm" value="{{ $b['min_age'] ?? 0 }}"></td>
                            <td><input type="number" min="0" max="120" name="periods[{{ $pi }}][brackets][{{ $bi }}][max_age]" class="form-control form-control-sm" value="{{ $b['max_age'] ?? '' }}" placeholder="∞"></td>
                            <td>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">€</span>
                                    <input type="number" step="0.01" min="0" name="periods[{{ $pi }}][brackets][{{ $bi }}][price]" class="form-control" value="{{ $b['price'] ?? 0 }}">
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch m-0">
                                    <input type="hidden" name="periods[{{ $pi }}][brackets][{{ $bi }}][counts_as_seat]" value="0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="periods[{{ $pi }}][brackets][{{ $bi }}][counts_as_seat]" value="1" @checked(!empty($b['counts_as_seat']))>
                                </div>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-light border" onclick="this.closest('tr').remove()" title="Rimuovi">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="form-text small mt-2">
            <i class="bi bi-info-circle me-1"></i>
            Disabilita "Conta come posto" per i bambini in braccio (es. infanti 0-2 anni).
        </div>
    </div>
</div>
