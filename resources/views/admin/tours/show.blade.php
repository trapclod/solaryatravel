@extends('layouts.admin')

@section('title', $tour->name)

@section('content')
    <div class="dash-page-header">
        <div>
            <h1>{{ $tour->name }}</h1>
            <p class="text-muted">{{ $tour->description_short }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.tours.edit', $tour) }}" class="btn btn-primary"><i class="bi bi-pencil me-1"></i>Modifica</a>
            <a href="{{ route('admin.tours.index') }}" class="btn btn-outline-secondary">Indietro</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h5>Descrizione</h5>
                    <p>{{ $tour->description }}</p>
                    @if ($tour->itinerary)
                        <h5 class="mt-3">Itinerario</h5>
                        <p>{!! nl2br(e($tour->itinerary)) !!}</p>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Fasce d'età</strong></div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light"><tr><th>Etichetta</th><th>Età</th><th>Prezzo</th><th>Posto</th></tr></thead>
                        <tbody>
                            @forelse ($tour->ageBrackets as $b)
                                <tr>
                                    <td>{{ $b->label }}</td>
                                    <td>{{ $b->range_label }}</td>
                                    <td>€{{ number_format($b->price, 2, ',', '.') }}</td>
                                    <td>{!! $b->counts_as_seat ? '<span class="badge text-bg-success">Sì</span>' : '<span class="badge text-bg-secondary">No</span>' !!}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center">Nessuna fascia configurata.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>Prossime partenze</strong>
                    <a href="{{ route('admin.tours.departures.index', $tour) }}" class="btn btn-sm btn-outline-primary">Gestisci</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light"><tr><th>Data</th><th>Orario</th><th>Stato</th><th>Posti</th></tr></thead>
                        <tbody>
                            @forelse ($tour->departures->take(20) as $d)
                                <tr>
                                    <td>{{ $d->departure_date->format('d/m/Y') }}</td>
                                    <td>{{ \Illuminate\Support\Str::of($d->start_time)->limit(5, '') }} – {{ \Illuminate\Support\Str::of($d->end_time)->limit(5, '') }}</td>
                                    <td><span class="badge text-bg-info">{{ $d->status }}</span></td>
                                    <td>{{ $d->seats_booked }} / {{ $d->capacity }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-muted text-center">Nessuna partenza programmata.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-5">Durata</dt><dd class="col-7">{{ $tour->duration_hours }}h</dd>
                        <dt class="col-5">Capacità</dt><dd class="col-7">{{ $tour->min_capacity }} – {{ $tour->max_capacity ?? $tour->total_capacity }}</dd>
                        <dt class="col-5">Punto partenza</dt><dd class="col-7">{{ $tour->departure_point ?? '—' }}</dd>
                        <dt class="col-5">Stagione</dt><dd class="col-7">
                            @if ($tour->season_start || $tour->season_end)
                                {{ optional($tour->season_start)->format('d/m') }} – {{ optional($tour->season_end)->format('d/m') }}
                            @else — @endif
                        </dd>
                        <dt class="col-5">Stato</dt><dd class="col-7">
                            @if ($tour->is_active)<span class="badge text-bg-success">Attivo</span>@else<span class="badge text-bg-secondary">Disattivo</span>@endif
                        </dd>
                    </dl>
                </div>
            </div>

            @if ($tour->catamarans->count())
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white"><strong>Catamarani assegnati</strong></div>
                    <ul class="list-group list-group-flush">
                        @foreach ($tour->catamarans as $cat)
                            <li class="list-group-item">{{ $cat->name }} <span class="text-muted small">({{ $cat->capacity }} posti)</span></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endsection
