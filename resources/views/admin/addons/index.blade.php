@extends('layouts.admin')

@section('title', 'Extra e servizi')

@php
    $priceTypes = [
        'per_person' => ['label' => 'Per persona', 'icon' => 'bi-person'],
        'per_booking' => ['label' => 'Per prenotazione', 'icon' => 'bi-receipt'],
        'per_unit' => ['label' => 'Per unità', 'icon' => 'bi-box-seam'],
    ];

    $totalActive = $addons->where('is_active', true)->count();
    $totalRevenue = $addons->sum(fn($a) => ($a->bookings_count ?? 0) * $a->price);
@endphp

@section('content')
    {{-- Page header --}}
    <div class="dash-page-header">
        <div>
            <h1>Extra e servizi aggiuntivi</h1>
            <p>Gestisci i servizi extra che i clienti possono aggiungere alle prenotazioni.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.addons.create') }}" class="btn btn-primary rounded-pill px-3 fw-semibold">
                <i class="bi bi-plus-lg me-2"></i>Nuovo extra
            </a>
        </div>
    </div>

    {{-- Mini stats --}}
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat is-active">
                <div class="dash-mini-stat-label"><i class="bi bi-stars me-1"></i>Totale</div>
                <div class="dash-mini-stat-value">{{ $addons->total() ?? $addons->count() }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-check-circle me-1"></i>Attivi</div>
                <div class="dash-mini-stat-value text-success">{{ $totalActive }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-pause-circle me-1"></i>Inattivi</div>
                <div class="dash-mini-stat-value text-muted">{{ ($addons->total() ?? $addons->count()) - $totalActive }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <div class="dash-mini-stat-label"><i class="bi bi-cash-coin me-1"></i>Ricavi stimati</div>
                <div class="dash-mini-stat-value text-warning">€{{ number_format($totalRevenue, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Addons table --}}
    <div class="dash-card mb-4">
        <div class="dash-card-header">
            <h3><i class="bi bi-list-ul me-2 text-primary"></i>Lista extra</h3>
            <span class="small text-muted"><i class="bi bi-arrows-move me-1"></i>Trascina per riordinare</span>
        </div>
        <div class="table-responsive">
            <table class="dash-table mb-0">
                <thead>
                    <tr>
                        <th style="width:40px"></th>
                        <th>Extra</th>
                        <th>Prezzo</th>
                        <th>Tipo</th>
                        <th class="text-center">Prenotazioni</th>
                        <th>Stato</th>
                        <th class="text-end">Azioni</th>
                    </tr>
                </thead>
                <tbody id="addons-sortable">
                    @forelse($addons as $addon)
                        <tr data-id="{{ $addon->id }}">
                            <td>
                                <span class="drag-handle text-muted" style="cursor:grab" title="Trascina per riordinare">
                                    <i class="bi bi-grip-vertical"></i>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($addon->image_path)
                                        <img src="{{ Storage::url($addon->image_path) }}" alt=""
                                             class="rounded-3 flex-shrink-0"
                                             style="width:48px; height:48px; object-fit:cover">
                                    @else
                                        <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                                             style="width:48px; height:48px">
                                            <i class="bi bi-stars fs-5"></i>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-dark">{{ $addon->name }}</div>
                                        @if($addon->description)
                                            <div class="small text-muted text-truncate" style="max-width:280px">
                                                {{ Str::limit($addon->description, 60) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="fw-bold text-dark">€{{ number_format($addon->price, 2, ',', '.') }}</td>
                            <td>
                                @php $pt = $priceTypes[$addon->price_type] ?? ['label' => $addon->price_type, 'icon' => 'bi-tag']; @endphp
                                <span class="small text-muted">
                                    <i class="bi {{ $pt['icon'] }} me-1"></i>{{ $pt['label'] }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">
                                    {{ $addon->bookings_count ?? 0 }}
                                </span>
                            </td>
                            <td>
                                @if($addon->is_active)
                                    <span class="status-pill s-confirmed"><i class="bi bi-check-circle-fill"></i>Attivo</span>
                                @else
                                    <span class="status-pill s-cancelled"><i class="bi bi-pause-circle-fill"></i>Inattivo</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-1">
                                    <form action="{{ route('admin.addons.toggle', $addon) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dash-icon-btn {{ $addon->is_active ? '' : 'is-success' }}"
                                                title="{{ $addon->is_active ? 'Disattiva' : 'Attiva' }}">
                                            <i class="bi {{ $addon->is_active ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.addons.show', $addon) }}" class="dash-icon-btn" title="Dettagli">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.addons.edit', $addon) }}" class="dash-icon-btn is-primary" title="Modifica">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.addons.destroy', $addon) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Eliminare {{ addslashes($addon->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dash-icon-btn is-danger" title="Elimina">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="text-center py-5">
                                    <div class="mx-auto mb-3 rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center"
                                         style="width:72px; height:72px">
                                        <i class="bi bi-stars fs-2"></i>
                                    </div>
                                    <h3 class="h5 fw-bold mb-2">Nessun extra configurato</h3>
                                    <p class="text-muted mb-3">Aggiungi servizi e attività che i clienti potranno selezionare in fase di prenotazione.</p>
                                    <a href="{{ route('admin.addons.create') }}" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                        <i class="bi bi-plus-lg me-2"></i>Aggiungi il primo extra
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($addons, 'hasPages') && $addons->hasPages())
            <div class="dash-card-body">
                {{ $addons->links() }}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('addons-sortable');
        if (!el || !el.children.length || !el.querySelector('[data-id]')) return;

        new Sortable(el, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'bg-light',
            onEnd: function () {
                const order = Array.from(el.querySelectorAll('tr[data-id]')).map(r => r.dataset.id);
                fetch('{{ route("admin.addons.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ order })
                });
            }
        });
    });
</script>
@endpush
