@extends('layouts.admin')

@section('title', 'Partenze: ' . $tour->name)

@section('content')
    <div class="dash-page-header">
        <div>
            <h1>Partenze – {{ $tour->name }}</h1>
            <p>Gestisci gli orari di partenza disponibili per ciascuna data.</p>
        </div>
        <a href="{{ route('admin.tours.edit', $tour) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Torna al tour</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><strong>Crea nuove partenze</strong></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.tours.departures.store', $tour) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Modalità</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="mode" id="mode-single" value="single" checked>
                                <label class="btn btn-outline-primary" for="mode-single">Singola data</label>
                                <input type="radio" class="btn-check" name="mode" id="mode-range" value="range">
                                <label class="btn btn-outline-primary" for="mode-range">Intervallo (batch)</label>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label">Data inizio *</label>
                                <input type="date" name="date_start" class="form-control" required min="{{ now()->toDateString() }}">
                            </div>
                            <div class="col-6 range-only" style="display:none;">
                                <label class="form-label">Data fine *</label>
                                <input type="date" name="date_end" class="form-control" min="{{ now()->toDateString() }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Orario partenza *</label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Orario rientro *</label>
                                <input type="time" name="end_time" class="form-control" required>
                            </div>
                            <div class="col-12 range-only" style="display:none;">
                                <label class="form-label">Giorni della settimana</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @php $days = ['Dom','Lun','Mar','Mer','Gio','Ven','Sab']; @endphp
                                    @foreach ($days as $i => $name)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="weekdays[]" value="{{ $i }}" id="wd-{{ $i }}" checked>
                                            <label class="form-check-label" for="wd-{{ $i }}">{{ $name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Modificatore prezzo</label>
                                <input type="number" step="0.01" min="0" max="10" name="price_modifier" class="form-control" value="1">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Capacità override</label>
                                <input type="number" min="0" name="capacity_override" class="form-control" placeholder="auto">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Note</label>
                                <input type="text" name="notes" class="form-control" maxlength="255">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-3">
                            <i class="bi bi-plus-lg me-1"></i>Crea partenze
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <form method="GET" class="row g-2">
                        <div class="col-5">
                            <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-5">
                            <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-2">
                            <button class="btn btn-sm btn-outline-primary w-100">Filtra</button>
                        </div>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Data</th><th>Orario</th><th>Stato</th><th>Posti</th><th>×Prezzo</th><th></th></tr>
                        </thead>
                        <tbody>
                            @forelse ($departures as $d)
                                <tr>
                                    <td>{{ $d->departure_date->format('D d/m/Y') }}</td>
                                    <td>{{ \Illuminate\Support\Str::of($d->start_time)->limit(5, '') }} – {{ \Illuminate\Support\Str::of($d->end_time)->limit(5, '') }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.tours.departures.update', [$tour, $d]) }}">
                                            @csrf @method('PUT')
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                @foreach (['scheduled', 'cancelled', 'sold_out'] as $st)
                                                    <option value="{{ $st }}" @selected($d->status === $st)>{{ $st }}</option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                    <td>{{ $d->seats_booked }} / {{ $d->capacity }}</td>
                                    <td>×{{ $d->price_modifier }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('admin.tours.departures.destroy', [$tour, $d]) }}" onsubmit="return confirm('Eliminare questa partenza?');" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">Nessuna partenza nell'intervallo selezionato.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
document.querySelectorAll('input[name="mode"]').forEach(input => {
    input.addEventListener('change', function() {
        const isRange = this.value === 'range';
        document.querySelectorAll('.range-only').forEach(el => {
            el.style.display = isRange ? '' : 'none';
        });
        document.querySelector('input[name="date_end"]').required = isRange;
    });
});
</script>
@endpush
@endsection
