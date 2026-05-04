{{-- Shared catamaran form fields. Uses $catamaran (model or null) --}}
@php
    $cat = $catamaran ?? null;
@endphp

<div class="row g-3">
    {{-- LEFT: main content --}}
    <div class="col-lg-8">
        {{-- Basic info --}}
        <div class="dash-card mb-3">
            <div class="dash-card-header">
                <h3><i class="bi bi-info-circle me-2 text-primary"></i>Informazioni base</h3>
            </div>
            <div class="dash-card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="name" class="form-label fw-semibold small">Nome <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" required
                               value="{{ old('name', $cat->name ?? '') }}"
                               class="form-control form-control-lg @error('name') is-invalid @enderror">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="sort_order" class="form-label fw-semibold small">Ordine</label>
                        <input type="number" name="sort_order" id="sort_order" min="0"
                               value="{{ old('sort_order', $cat->sort_order ?? 0) }}"
                               class="form-control form-control-lg">
                    </div>
                    <div class="col-12">
                        <label for="slug" class="form-label fw-semibold small">Slug URL</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted">/catamarani/</span>
                            <input type="text" name="slug" id="slug"
                                   value="{{ old('slug', $cat->slug ?? '') }}"
                                   placeholder="generato-automaticamente"
                                   class="form-control @error('slug') is-invalid @enderror">
                        </div>
                        @error('slug')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="description_short" class="form-label fw-semibold small">
                            Descrizione breve
                            <span class="text-muted fw-normal">(max 500 caratteri)</span>
                        </label>
                        <textarea name="description_short" id="description_short" rows="2" maxlength="500"
                                  class="form-control">{{ old('description_short', $cat->description_short ?? '') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label fw-semibold small">Descrizione completa</label>
                        <textarea name="description" id="description" rows="5"
                                  class="form-control">{{ old('description', $cat->description ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Specs --}}
        <div class="dash-card mb-3">
            <div class="dash-card-header">
                <h3><i class="bi bi-rulers me-2 text-primary"></i>Specifiche tecniche</h3>
            </div>
            <div class="dash-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="capacity" class="form-label fw-semibold small">
                            Capacità (posti) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-people"></i></span>
                            <input type="number" name="capacity" id="capacity" required min="1" max="100"
                                   value="{{ old('capacity', $cat->capacity ?? 12) }}"
                                   class="form-control @error('capacity') is-invalid @enderror">
                        </div>
                        @error('capacity')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="length_meters" class="form-label fw-semibold small">Lunghezza</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-arrows-fullscreen"></i></span>
                            <input type="number" name="length_meters" id="length_meters" step="0.1" min="0"
                                   value="{{ old('length_meters', $cat->length_meters ?? '') }}"
                                   class="form-control">
                            <span class="input-group-text bg-light">m</span>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <label class="form-label fw-semibold small d-flex align-items-center justify-content-between">
                    <span><i class="bi bi-stars me-1 text-warning"></i>Caratteristiche</span>
                    <button type="button" id="add-feature" class="btn btn-sm btn-light rounded-pill border fw-medium">
                        <i class="bi bi-plus-lg me-1"></i>Aggiungi
                    </button>
                </label>
                <div id="features-container" class="d-flex flex-column gap-2">
                    @php
                        $rawFeatures = old('features', $cat->features ?? []);
                        $features = is_string($rawFeatures) ? (json_decode($rawFeatures, true) ?? []) : (is_array($rawFeatures) ? $rawFeatures : []);
                        if (empty($features)) $features = [''];
                    @endphp
                    @foreach($features as $feature)
                        <div class="input-group feature-row">
                            <span class="input-group-text bg-light"><i class="bi bi-check2 text-success"></i></span>
                            <input type="text" name="features[]" value="{{ $feature }}"
                                   placeholder="es. Solarium, Snorkeling, Bar..."
                                   class="form-control">
                            <button type="button" class="btn btn-light border remove-feature" title="Rimuovi">
                                <i class="bi bi-trash text-danger"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Pricing --}}
        <div class="dash-card mb-3">
            <div class="dash-card-header">
                <h3><i class="bi bi-currency-euro me-2 text-warning"></i>Prezzi</h3>
                <span class="small text-muted">Tutti i valori sono in euro</span>
            </div>
            <div class="dash-card-body">
                <div class="alert alert-info bg-info-subtle border-0 small mb-3" style="border-radius:.65rem">
                    <i class="bi bi-info-circle me-2"></i>
                    Compila almeno i prezzi base. I prezzi <strong>esclusivi</strong> si applicano alla prenotazione dell'intero catamarano,
                    mentre i prezzi <strong>per persona</strong> sono usati per le condivisioni a posto.
                </div>

                <h6 class="fw-bold text-uppercase small text-muted mb-2" style="letter-spacing:.05em">
                    <i class="bi bi-tag me-1"></i>Prezzi base
                </h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="base_price_half_day" class="form-label fw-semibold small">
                            Mezza giornata <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">€</span>
                            <input type="number" name="base_price_half_day" id="base_price_half_day"
                                   required step="0.01" min="0"
                                   value="{{ old('base_price_half_day', $cat->base_price_half_day ?? '') }}"
                                   class="form-control @error('base_price_half_day') is-invalid @enderror">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="base_price_full_day" class="form-label fw-semibold small">
                            Giornata intera <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">€</span>
                            <input type="number" name="base_price_full_day" id="base_price_full_day"
                                   required step="0.01" min="0"
                                   value="{{ old('base_price_full_day', $cat->base_price_full_day ?? '') }}"
                                   class="form-control @error('base_price_full_day') is-invalid @enderror">
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-uppercase small text-muted mb-2 mt-4" style="letter-spacing:.05em">
                    <i class="bi bi-gem me-1"></i>Prezzi esclusivi <span class="text-muted text-lowercase fw-normal">(intera barca)</span>
                </h6>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="exclusive_price_half_day" class="form-label fw-semibold small">Mezza giornata</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">€</span>
                            <input type="number" name="exclusive_price_half_day" id="exclusive_price_half_day"
                                   step="0.01" min="0"
                                   value="{{ old('exclusive_price_half_day', $cat->exclusive_price_half_day ?? '') }}"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="exclusive_price_full_day" class="form-label fw-semibold small">Giornata intera</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">€</span>
                            <input type="number" name="exclusive_price_full_day" id="exclusive_price_full_day"
                                   step="0.01" min="0"
                                   value="{{ old('exclusive_price_full_day', $cat->exclusive_price_full_day ?? '') }}"
                                   class="form-control">
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-uppercase small text-muted mb-2 mt-4" style="letter-spacing:.05em">
                    <i class="bi bi-person me-1"></i>Prezzi per persona
                </h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="price_per_person_half_day" class="form-label fw-semibold small">Mezza giornata</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">€</span>
                            <input type="number" name="price_per_person_half_day" id="price_per_person_half_day"
                                   step="0.01" min="0"
                                   value="{{ old('price_per_person_half_day', $cat->price_per_person_half_day ?? '') }}"
                                   class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="price_per_person_full_day" class="form-label fw-semibold small">Giornata intera</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">€</span>
                            <input type="number" name="price_per_person_full_day" id="price_per_person_full_day"
                                   step="0.01" min="0"
                                   value="{{ old('price_per_person_full_day', $cat->price_per_person_full_day ?? '') }}"
                                   class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SEO --}}
        <div class="dash-card mb-3">
            <div class="dash-card-header">
                <h3><i class="bi bi-search me-2 text-primary"></i>SEO</h3>
            </div>
            <div class="dash-card-body">
                <div class="mb-3">
                    <label for="meta_title" class="form-label fw-semibold small">Meta title</label>
                    <input type="text" name="meta_title" id="meta_title" maxlength="255"
                           value="{{ old('meta_title', $cat->meta_title ?? '') }}"
                           class="form-control">
                    <div class="form-text small">Massimo 60 caratteri raccomandati per Google.</div>
                </div>
                <div>
                    <label for="meta_description" class="form-label fw-semibold small">Meta description</label>
                    <textarea name="meta_description" id="meta_description" rows="2" maxlength="500"
                              class="form-control">{{ old('meta_description', $cat->meta_description ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: sidebar --}}
    <div class="col-lg-4">
        <div class="dash-card mb-3" style="position:sticky; top:1rem">
            <div class="dash-card-header">
                <h3><i class="bi bi-toggles me-2 text-primary"></i>Stato pubblicazione</h3>
            </div>
            <div class="dash-card-body">
                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3">
                    <div>
                        <div class="fw-semibold mb-1">Catamarano attivo</div>
                        <p class="small text-muted mb-0">Visibile sul sito pubblico e prenotabile.</p>
                    </div>
                    <div class="form-check form-switch m-0" style="font-size:1.4rem">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active"
                               name="is_active" value="1"
                               {{ old('is_active', $cat->is_active ?? true) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>
        </div>

        @if($cat)
            <div class="dash-card mb-3">
                <div class="dash-card-header">
                    <h3><i class="bi bi-bar-chart me-2 text-primary"></i>Statistiche</h3>
                </div>
                <div class="dash-card-body">
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <span class="small text-muted"><i class="bi bi-journal-bookmark me-2"></i>Prenotazioni totali</span>
                        <span class="fw-bold">{{ $cat->bookings()->count() }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                        <span class="small text-muted"><i class="bi bi-image me-2"></i>Immagini</span>
                        <span class="fw-bold">{{ $cat->images->count() }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between py-2">
                        <span class="small text-muted"><i class="bi bi-clock me-2"></i>Creato</span>
                        <span class="fw-medium small">{{ $cat->created_at?->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('features-container');

        function makeRow() {
            const row = document.createElement('div');
            row.className = 'input-group feature-row';
            row.innerHTML = `
                <span class="input-group-text bg-light"><i class="bi bi-check2 text-success"></i></span>
                <input type="text" name="features[]" placeholder="es. Solarium, Snorkeling, Bar..." class="form-control">
                <button type="button" class="btn btn-light border remove-feature" title="Rimuovi"><i class="bi bi-trash text-danger"></i></button>
            `;
            return row;
        }

        document.getElementById('add-feature').addEventListener('click', () => {
            const row = makeRow();
            container.appendChild(row);
            row.querySelector('input').focus();
        });

        container.addEventListener('click', (e) => {
            const btn = e.target.closest('.remove-feature');
            if (!btn) return;
            const rows = container.querySelectorAll('.feature-row');
            if (rows.length > 1) {
                btn.closest('.feature-row').remove();
            } else {
                btn.closest('.feature-row').querySelector('input').value = '';
            }
        });
    });
</script>
@endpush
