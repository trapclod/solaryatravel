{{--
    Form condiviso create/edit tour.
    Variabili attese: $tour (Tour, può essere nuova istanza), $catamarans (Collection),
                      $selectedCatamarans (array di id).
--}}
@php
    $isEdit = $tour->exists;
    $included = old('included', $tour->included ?? []);
    $excluded = old('excluded', $tour->excluded ?? []);
@endphp

<div class="row g-4">
    {{-- Colonna sinistra: dettagli --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><strong>Informazioni base</strong></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Nome tour *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $tour->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $tour->slug) }}" placeholder="auto-generato">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Descrizione breve</label>
                        <textarea name="description_short" rows="2" class="form-control" maxlength="500">{{ old('description_short', $tour->description_short) }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Descrizione completa</label>
                        <textarea name="description" rows="5" class="form-control">{{ old('description', $tour->description) }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Itinerario</label>
                        <textarea name="itinerary" rows="4" class="form-control" placeholder="Descrivi le tappe del tour...">{{ old('itinerary', $tour->itinerary) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><strong>Dettagli operativi</strong></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Durata (ore)</label>
                        <input type="number" step="0.5" min="0" max="48" name="duration_hours" class="form-control" value="{{ old('duration_hours', $tour->duration_hours) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Capacità min</label>
                        <input type="number" min="1" name="min_capacity" class="form-control" value="{{ old('min_capacity', $tour->min_capacity ?? 1) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Capacità max</label>
                        <input type="number" min="1" name="max_capacity" class="form-control" value="{{ old('max_capacity', $tour->max_capacity) }}" placeholder="auto = somma catamarani">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ordine</label>
                        <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $tour->sort_order ?? 0) }}">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Punto di partenza</label>
                        <input type="text" name="departure_point" class="form-control" value="{{ old('departure_point', $tour->departure_point) }}" placeholder="es. Marina di Salivoli">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Inizio stagione</label>
                        <input type="date" name="season_start" class="form-control" value="{{ old('season_start', optional($tour->season_start)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fine stagione</label>
                        <input type="date" name="season_end" class="form-control" value="{{ old('season_end', optional($tour->season_end)->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Inclusi / Esclusi --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><strong>Cosa è incluso / escluso</strong></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Incluso</label>
                        <div id="included-list">
                            @forelse ($included as $i => $item)
                                <div class="input-group mb-2">
                                    <input type="text" name="included[]" class="form-control" value="{{ $item }}">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()"><i class="bi bi-x"></i></button>
                                </div>
                            @empty
                            @endforelse
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addItem('included-list', 'included[]')">
                            <i class="bi bi-plus"></i> Aggiungi voce
                        </button>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Escluso</label>
                        <div id="excluded-list">
                            @forelse ($excluded as $i => $item)
                                <div class="input-group mb-2">
                                    <input type="text" name="excluded[]" class="form-control" value="{{ $item }}">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()"><i class="bi bi-x"></i></button>
                                </div>
                            @empty
                            @endforelse
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addItem('excluded-list', 'excluded[]')">
                            <i class="bi bi-plus"></i> Aggiungi voce
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Fasce d'età --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Fasce d'età e prezzi</strong>
                <button type="button" class="btn btn-sm btn-primary" onclick="addAgeBracket()">
                    <i class="bi bi-plus"></i> Aggiungi fascia
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:25%">Etichetta *</th>
                                <th style="width:13%">Età min</th>
                                <th style="width:13%">Età max</th>
                                <th style="width:18%">Prezzo (€) *</th>
                                <th style="width:18%">Conta come posto</th>
                                <th style="width:13%"></th>
                            </tr>
                        </thead>
                        <tbody id="age-brackets-body">
                            @php
                                $bracketsOld = old('age_brackets', $tour->ageBrackets->map(fn($b) => [
                                    'id' => $b->id,
                                    'label' => $b->label,
                                    'min_age' => $b->min_age,
                                    'max_age' => $b->max_age,
                                    'price' => $b->price,
                                    'counts_as_seat' => $b->counts_as_seat,
                                ])->toArray());
                            @endphp
                            @foreach ($bracketsOld as $i => $b)
                                <tr>
                                    <td>
                                        <input type="hidden" name="age_brackets[{{ $i }}][id]" value="{{ $b['id'] ?? '' }}">
                                        <input type="text" name="age_brackets[{{ $i }}][label]" class="form-control form-control-sm" value="{{ $b['label'] ?? '' }}" placeholder="es. Adulto">
                                    </td>
                                    <td><input type="number" min="0" max="120" name="age_brackets[{{ $i }}][min_age]" class="form-control form-control-sm" value="{{ $b['min_age'] ?? 0 }}"></td>
                                    <td><input type="number" min="0" max="120" name="age_brackets[{{ $i }}][max_age]" class="form-control form-control-sm" value="{{ $b['max_age'] ?? '' }}" placeholder="∞"></td>
                                    <td><input type="number" step="0.01" min="0" name="age_brackets[{{ $i }}][price]" class="form-control form-control-sm" value="{{ $b['price'] ?? 0 }}"></td>
                                    <td>
                                        <div class="form-check form-switch mt-1">
                                            <input type="hidden" name="age_brackets[{{ $i }}][counts_as_seat]" value="0">
                                            <input class="form-check-input" type="checkbox" name="age_brackets[{{ $i }}][counts_as_seat]" value="1" @checked(!empty($b['counts_as_seat']))>
                                        </div>
                                    </td>
                                    <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-muted small p-3 border-top">
                    <i class="bi bi-info-circle me-1"></i>
                    Disabilita "Conta come posto" per i bambini in braccio (es. infanti 0-2 anni).
                </div>
            </div>
        </div>

        {{-- SEO --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><strong>SEO</strong></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Meta title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $tour->meta_title) }}" maxlength="255">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Meta description</label>
                        <textarea name="meta_description" rows="2" class="form-control" maxlength="500">{{ old('meta_description', $tour->meta_description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Colonna destra: pubblicazione, immagini, catamarani --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><strong>Pubblicazione</strong></div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $tour->is_active ?? true))>
                    <label class="form-check-label" for="is_active">Attivo</label>
                </div>
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i>{{ $isEdit ? 'Aggiorna' : 'Crea' }} tour</button>
                <a href="{{ route('admin.tours.index') }}" class="btn btn-outline-secondary w-100 mt-2">Annulla</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white"><strong>Catamarani assegnati</strong></div>
            <div class="card-body">
                <p class="text-muted small mb-2">Se non selezioni nessun catamarano, il tour può operare con qualsiasi catamarano attivo.</p>
                @foreach ($catamarans as $cat)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="catamarans[]" value="{{ $cat->id }}" id="cat-{{ $cat->id }}" @checked(in_array($cat->id, old('catamarans', $selectedCatamarans)))>
                        <label class="form-check-label" for="cat-{{ $cat->id }}">
                            {{ $cat->name }} <span class="text-muted small">({{ $cat->capacity }} posti)</span>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        @if ($isEdit)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Immagini</strong></div>
                <div class="card-body">
                    @if ($tour->images->count())
                        <div class="row g-2 mb-3">
                            @foreach ($tour->images as $img)
                                <div class="col-6 position-relative">
                                    <img src="{{ $img->url }}" class="img-fluid rounded" style="aspect-ratio: 4/3; object-fit:cover;">
                                    <div class="position-absolute top-0 end-0 p-1 d-flex flex-column gap-1">
                                        @if (!$img->is_primary)
                                            <button type="button" class="btn btn-sm btn-light" title="Imposta come principale" onclick="document.getElementById('img-primary-{{ $img->id }}').submit()">
                                                <i class="bi bi-star"></i>
                                            </button>
                                        @else
                                            <span class="badge text-bg-primary"><i class="bi bi-star-fill"></i></span>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger" title="Elimina" onclick="if(confirm('Eliminare?')) document.getElementById('img-del-{{ $img->id }}').submit()">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <label class="form-label">Carica nuove immagini</label>
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                    <small class="text-muted">Max 5MB ciascuna.</small>
                </div>
            </div>
        @else
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Immagini</strong></div>
                <div class="card-body">
                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                    <small class="text-muted">Le immagini possono essere caricate dopo la creazione.</small>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Form esterni per azioni immagini (necessari fuori dal form principale per evitare nested forms) --}}
@if ($isEdit)
    @push('end-of-body')
        @foreach ($tour->images as $img)
            <form id="img-del-{{ $img->id }}" method="POST" action="{{ route('admin.tours.images.delete', [$tour, $img]) }}" class="d-none">
                @csrf @method('DELETE')
            </form>
            <form id="img-primary-{{ $img->id }}" method="POST" action="{{ route('admin.tours.images.primary', [$tour, $img]) }}" class="d-none">
                @csrf
            </form>
        @endforeach
    @endpush
@endif

@push('scripts')
<script>
function addItem(listId, name) {
    const list = document.getElementById(listId);
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `<input type="text" name="${name}" class="form-control"><button type="button" class="btn btn-outline-danger" onclick="this.closest('.input-group').remove()"><i class="bi bi-x"></i></button>`;
    list.appendChild(div);
}

let bracketIndex = {{ count($bracketsOld ?? []) }};
function addAgeBracket() {
    const tbody = document.getElementById('age-brackets-body');
    const i = bracketIndex++;
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <input type="hidden" name="age_brackets[${i}][id]" value="">
            <input type="text" name="age_brackets[${i}][label]" class="form-control form-control-sm" placeholder="es. Adulto" required>
        </td>
        <td><input type="number" min="0" max="120" name="age_brackets[${i}][min_age]" class="form-control form-control-sm" value="0"></td>
        <td><input type="number" min="0" max="120" name="age_brackets[${i}][max_age]" class="form-control form-control-sm" placeholder="∞"></td>
        <td><input type="number" step="0.01" min="0" name="age_brackets[${i}][price]" class="form-control form-control-sm" value="0" required></td>
        <td>
            <div class="form-check form-switch mt-1">
                <input type="hidden" name="age_brackets[${i}][counts_as_seat]" value="0">
                <input class="form-check-input" type="checkbox" name="age_brackets[${i}][counts_as_seat]" value="1" checked>
            </div>
        </td>
        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
}
</script>
@endpush
