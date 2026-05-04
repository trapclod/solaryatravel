{{-- Shared form fields for discount codes --}}
@php $d = $discount ?? null; @endphp

<div class="row g-3">
    {{-- LEFT COLUMN --}}
    <div class="col-lg-8">
        {{-- Code & description --}}
        <div class="dash-card mb-3">
            <div class="dash-card-header">
                <h3><i class="bi bi-ticket-perforated me-2 text-warning"></i>Codice e descrizione</h3>
            </div>
            <div class="dash-card-body">
                <div class="mb-3">
                    <label for="code" class="form-label fw-semibold">Codice <span class="text-danger">*</span></label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-warning-subtle text-warning border-end-0"><i class="bi bi-tag-fill"></i></span>
                        <input type="text" name="code" id="code"
                               value="{{ old('code', $d->code ?? '') }}"
                               class="form-control font-monospace text-uppercase fw-bold @error('code') is-invalid @enderror"
                               placeholder="ES. SUMMER2026" required
                               style="letter-spacing:0.05em">
                        <button type="button" onclick="generateCode()" class="btn btn-light border" title="Genera codice casuale">
                            <i class="bi bi-shuffle me-1"></i>Genera
                        </button>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-text"><i class="bi bi-info-circle me-1"></i>Convertito automaticamente in maiuscolo</div>
                </div>

                <div>
                    <label for="description" class="form-label fw-semibold">Descrizione</label>
                    <input type="text" name="description" id="description"
                           value="{{ old('description', $d->description ?? '') }}"
                           class="form-control @error('description') is-invalid @enderror"
                           placeholder="es. Sconto estate 2026">
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Discount details --}}
        <div class="dash-card mb-3">
            <div class="dash-card-header">
                <h3><i class="bi bi-percent me-2 text-primary"></i>Dettagli sconto</h3>
            </div>
            <div class="dash-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="discount_type" class="form-label fw-semibold">Tipo sconto <span class="text-danger">*</span></label>
                        <select name="discount_type" id="discount_type"
                                class="form-select @error('discount_type') is-invalid @enderror" required>
                            <option value="percentage" {{ old('discount_type', $d->discount_type ?? 'percentage') == 'percentage' ? 'selected' : '' }}>📊 Percentuale (%)</option>
                            <option value="fixed" {{ old('discount_type', $d->discount_type ?? '') == 'fixed' ? 'selected' : '' }}>💶 Fisso (€)</option>
                        </select>
                        @error('discount_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="discount_value" class="form-label fw-semibold">Valore <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="discount_value" id="discount_value"
                                   value="{{ old('discount_value', $d->discount_value ?? '') }}"
                                   step="0.01" min="0"
                                   class="form-control fw-bold fs-5 @error('discount_value') is-invalid @enderror" required>
                            <span class="input-group-text fw-bold" id="discount_suffix">%</span>
                            @error('discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="min_amount" class="form-label fw-semibold">Importo minimo</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" name="min_amount" id="min_amount"
                                   value="{{ old('min_amount', $d->min_amount ?? '') }}"
                                   step="0.01" min="0"
                                   class="form-control @error('min_amount') is-invalid @enderror"
                                   placeholder="Nessun minimo">
                            @error('min_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text">Importo minimo dell'ordine</div>
                    </div>

                    <div class="col-md-6">
                        <label for="max_discount" class="form-label fw-semibold">Sconto massimo</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" name="max_discount" id="max_discount"
                                   value="{{ old('max_discount', $d->max_discount ?? '') }}"
                                   step="0.01" min="0"
                                   class="form-control @error('max_discount') is-invalid @enderror"
                                   placeholder="Nessun limite">
                            @error('max_discount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text">Solo per sconti percentuali</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Usage limits --}}
        <div class="dash-card mb-3">
            <div class="dash-card-header">
                <h3><i class="bi bi-stack me-2 text-primary"></i>Limiti di utilizzo</h3>
            </div>
            <div class="dash-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="usage_limit" class="form-label fw-semibold">Utilizzi totali</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-people"></i></span>
                            <input type="number" name="usage_limit" id="usage_limit"
                                   value="{{ old('usage_limit', $d->usage_limit ?? '') }}"
                                   min="1"
                                   class="form-control @error('usage_limit') is-invalid @enderror"
                                   placeholder="Illimitati">
                            @error('usage_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text">Numero massimo totale di utilizzi</div>
                    </div>

                    <div class="col-md-6">
                        <label for="user_limit" class="form-label fw-semibold">Utilizzi per utente</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="number" name="user_limit" id="user_limit"
                                   value="{{ old('user_limit', $d->user_limit ?? 1) }}"
                                   min="1"
                                   class="form-control @error('user_limit') is-invalid @enderror">
                            @error('user_limit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-text">Quante volte un singolo utente può usare il codice</div>
                    </div>
                </div>

                @if(isset($d) && $d && $d->usage_count > 0)
                    <div class="alert alert-info border-0 d-flex align-items-center gap-2 mt-3 mb-0">
                        <i class="bi bi-info-circle-fill"></i>
                        <div>
                            Questo codice è già stato utilizzato <strong>{{ $d->usage_count }}</strong>
                            {{ $d->usage_count == 1 ? 'volta' : 'volte' }}.
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Validity period --}}
        <div class="dash-card mb-3">
            <div class="dash-card-header">
                <h3><i class="bi bi-calendar-range me-2 text-primary"></i>Periodo di validità</h3>
            </div>
            <div class="dash-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="valid_from" class="form-label fw-semibold">Valido da</label>
                        <input type="datetime-local" name="valid_from" id="valid_from"
                               value="{{ old('valid_from', isset($d->valid_from) ? $d->valid_from->format('Y-m-d\TH:i') : '') }}"
                               class="form-control @error('valid_from') is-invalid @enderror">
                        @error('valid_from') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Vuoto = attivazione immediata</div>
                    </div>

                    <div class="col-md-6">
                        <label for="valid_until" class="form-label fw-semibold">Valido fino a</label>
                        <input type="datetime-local" name="valid_until" id="valid_until"
                               value="{{ old('valid_until', isset($d->valid_until) ? $d->valid_until->format('Y-m-d\TH:i') : '') }}"
                               class="form-control @error('valid_until') is-invalid @enderror">
                        @error('valid_until') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Vuoto = nessuna scadenza</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT SIDEBAR --}}
    <div class="col-lg-4">
        <div style="position:sticky; top:1rem">
            <div class="dash-card mb-3">
                <div class="dash-card-header">
                    <h3><i class="bi bi-toggles me-2 text-primary"></i>Stato</h3>
                </div>
                <div class="dash-card-body">
                    <div class="bg-light rounded-3 p-3">
                        <input type="hidden" name="is_active" value="0">
                        <div class="form-check form-switch form-switch-lg d-flex align-items-center gap-2 mb-0">
                            <input class="form-check-input" type="checkbox" id="is_active"
                                   name="is_active" value="1"
                                   {{ old('is_active', $d->is_active ?? true) ? 'checked' : '' }}
                                   style="width:2.75em; height:1.5em">
                            <label class="form-check-label fw-semibold ms-2" for="is_active">
                                Codice attivo
                            </label>
                        </div>
                        <div class="form-text mt-2 mb-0">Solo i codici attivi sono utilizzabili dai clienti.</div>
                    </div>
                </div>
            </div>

            @if(isset($d) && $d)
                <div class="dash-card mb-3">
                    <div class="dash-card-header">
                        <h3><i class="bi bi-bar-chart me-2 text-primary"></i>Statistiche</h3>
                    </div>
                    <div class="dash-card-body">
                        <div class="d-flex justify-content-between py-2 border-bottom small">
                            <span class="text-muted"><i class="bi bi-graph-up me-2"></i>Utilizzi</span>
                            <span class="fw-bold">{{ $d->usage_count }}{{ $d->usage_limit ? ' / '.$d->usage_limit : '' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 small">
                            <span class="text-muted"><i class="bi bi-calendar-plus me-2"></i>Creato</span>
                            <span class="text-dark">{{ $d->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="dash-card mb-3">
                <div class="dash-card-header">
                    <h3><i class="bi bi-lightbulb me-2 text-warning"></i>Suggerimenti</h3>
                </div>
                <div class="dash-card-body small text-muted">
                    <ul class="ps-3 mb-0">
                        <li class="mb-1">Usa codici facili da ricordare</li>
                        <li class="mb-1">Imposta un periodo di validità per creare urgenza</li>
                        <li class="mb-0">Limita gli utilizzi per controllare i costi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const typeEl = document.getElementById('discount_type');
        const suffixEl = document.getElementById('discount_suffix');

        function updateSuffix() {
            suffixEl.textContent = typeEl.value === 'percentage' ? '%' : '€';
        }

        typeEl.addEventListener('change', updateSuffix);
        updateSuffix();
    })();

    function generateCode() {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        let code = '';
        for (let i = 0; i < 8; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        document.getElementById('code').value = code;
    }
</script>
@endpush
