<div>
    <style>
        .bk-card { background: #fff; border-radius: 1rem; box-shadow: 0 4px 16px rgba(14,27,51,.06); padding: 1.5rem; }
        .bk-card + .bk-card { margin-top: 1rem; }
        .bk-card h3 { font-size: 1.05rem; font-weight: 700; margin-bottom: 1rem; color: #0E1B33; }
        .bk-card h3 i { color: #7C37FF; margin-right: .5rem; }

        .bk-bracket-row { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .85rem 0; border-bottom: 1px solid #f1f3f5; }
        .bk-bracket-row:last-child { border-bottom: 0; }
        .bk-bracket-info strong { display: block; color: #0E1B33; font-size: .98rem; }
        .bk-bracket-info small { color: #6c757d; font-size: .8rem; }
        .bk-bracket-price { color: #7C37FF; font-weight: 600; font-size: .9rem; }

        .bk-stepper { display: inline-flex; align-items: center; background: #f8f9fc; border-radius: 50px; padding: 4px; }
        .bk-stepper button { width: 32px; height: 32px; border-radius: 50%; border: 0; background: #fff; color: #0E1B33; font-weight: 700; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
        .bk-stepper button:disabled { opacity: .35; cursor: not-allowed; }
        .bk-stepper button.plus { background: #7C37FF; color: #fff; }
        .bk-stepper .qty { min-width: 32px; text-align: center; font-weight: 700; font-size: 1rem; }

        .bk-addon { display: flex; align-items: flex-start; gap: .85rem; padding: .85rem; border: 1.5px solid #eef0f3; border-radius: .75rem; cursor: pointer; transition: all .15s; }
        .bk-addon:hover { border-color: #d0c2f7; }
        .bk-addon.active { border-color: #7C37FF; background: #f7f3ff; }
        .bk-addon input { margin-top: .25rem; }
        .bk-addon-info strong { display: block; color: #0E1B33; }
        .bk-addon-info small { color: #6c757d; font-size: .82rem; }
        .bk-addon-price { margin-left: auto; color: #7C37FF; font-weight: 700; white-space: nowrap; }

        .bk-summary { position: sticky; top: 90px; }
        .bk-summary-line { display: flex; justify-content: space-between; padding: .35rem 0; font-size: .92rem; }
        .bk-summary-line.total { border-top: 1.5px solid #eef0f3; margin-top: .5rem; padding-top: .85rem; font-size: 1.1rem; font-weight: 700; color: #0E1B33; }
        .bk-summary-line.discount { color: #198754; }

        .bk-input { border-radius: 50px; padding: .65rem 1.1rem; border: 1.5px solid #eef0f3; width: 100%; }
        .bk-input:focus { outline: none; border-color: #7C37FF; box-shadow: 0 0 0 3px rgba(124,55,255,.12); }
        .bk-textarea { border-radius: 1rem; padding: .85rem 1.1rem; border: 1.5px solid #eef0f3; width: 100%; min-height: 100px; resize: vertical; }
        .bk-label { font-size: .85rem; font-weight: 600; color: #0E1B33; margin-bottom: .35rem; display: block; }

        .bk-submit { width: 100%; background: #7C37FF; color: #fff; border: 0; border-radius: 50px; padding: 1rem; font-weight: 700; font-size: 1rem; }
        .bk-submit:disabled { background: #c7b8e8; cursor: not-allowed; }

        .bk-departure-info { background: linear-gradient(135deg,#7C37FF,#5b1fd8); color: #fff; border-radius: 1rem; padding: 1.25rem; margin-bottom: 1rem; }
        .bk-departure-info .date { font-size: 1.15rem; font-weight: 700; }
        .bk-departure-info .time { font-size: .95rem; opacity: .9; }
        .bk-departure-info .seats { font-size: .85rem; opacity: .85; margin-top: .35rem; }
    </style>

    <div class="container py-5" style="margin-top: 90px;">
        <div class="row g-4">
            {{-- LEFT --}}
            <div class="col-lg-8">
                {{-- Tour header --}}
                <div class="bk-card">
                    <div class="d-flex align-items-center gap-3">
                        @if ($tour->primary_image)
                            <img src="{{ Storage::url($tour->primary_image->image_path) }}" alt="" style="width:80px;height:80px;border-radius:.75rem;object-fit:cover;">
                        @endif
                        <div>
                            <small class="text-muted text-uppercase">Stai prenotando</small>
                            <h2 class="mb-0" style="font-size:1.4rem;font-weight:700;color:#0E1B33;">{{ $tour->name }}</h2>
                            @if ($tour->duration_hours)
                                <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $tour->duration_hours }}h</small>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Departure info --}}
                @if ($departure)
                    <div class="bk-departure-info mt-3">
                        <div class="date"><i class="bi bi-calendar-event me-2"></i>{{ \Carbon\Carbon::parse($departure->departure_date)->translatedFormat('l d F Y') }}</div>
                        <div class="time"><i class="bi bi-clock me-2"></i>{{ \Carbon\Carbon::parse($departure->start_time)->format('H:i') }}@if ($departure->end_time) – {{ \Carbon\Carbon::parse($departure->end_time)->format('H:i') }}@endif</div>
                        <div class="seats">{{ $departure->seats_available }} posti disponibili</div>
                    </div>
                @else
                    <div class="alert alert-warning mt-3">
                        Nessuna partenza selezionata. Torna alla pagina del tour e scegli una data.
                    </div>
                @endif

                {{-- Partecipanti --}}
                @if ($departure && $this->brackets->isNotEmpty())
                    <div class="bk-card">
                        <h3><i class="bi bi-people-fill"></i>Partecipanti</h3>
                        @foreach ($this->brackets as $b)
                            <div class="bk-bracket-row">
                                <div class="bk-bracket-info">
                                    <strong>{{ $b->label }}</strong>
                                    <small>
                                        @if ($b->min_age && $b->max_age)
                                            {{ $b->min_age }}–{{ $b->max_age }} anni
                                        @elseif ($b->max_age)
                                            fino a {{ $b->max_age }} anni
                                        @elseif ($b->min_age)
                                            da {{ $b->min_age }} anni
                                        @endif
                                        @if (!$b->counts_as_seat) · in braccio @endif
                                    </small>
                                    <div class="bk-bracket-price mt-1">€ {{ number_format((float) $b->price * (float) $departure->price_modifier, 2, ',', '.') }}</div>
                                </div>
                                <div class="bk-stepper">
                                    <button type="button" wire:click="decrement({{ $b->id }})" @disabled(($bracketCounts[$b->id] ?? 0) <= 0)>−</button>
                                    <span class="qty">{{ $bracketCounts[$b->id] ?? 0 }}</span>
                                    <button type="button" class="plus" wire:click="increment({{ $b->id }})">+</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Addon --}}
                @if ($departure && $this->addons->isNotEmpty())
                    <div class="bk-card">
                        <h3><i class="bi bi-stars"></i>Servizi extra</h3>
                        <div class="d-flex flex-column gap-2">
                            @foreach ($this->addons as $addon)
                                @php $isActive = in_array($addon->id, $selectedAddons, true); @endphp
                                <label class="bk-addon {{ $isActive ? 'active' : '' }}" wire:key="addon-{{ $addon->id }}">
                                    <input type="checkbox" wire:click="toggleAddon({{ $addon->id }})" @checked($isActive)>
                                    <div class="bk-addon-info">
                                        <strong>{{ $addon->name }}</strong>
                                        @if ($addon->description)
                                            <small>{{ $addon->description }}</small>
                                        @endif
                                    </div>
                                    <div class="bk-addon-price">
                                        € {{ number_format((float) $addon->price, 2, ',', '.') }}
                                        <small class="d-block text-muted fw-normal" style="font-size:.75rem">{{ $addon->getPriceTypeLabel() }}</small>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Dati cliente --}}
                @if ($departure)
                    <div class="bk-card">
                        <h3><i class="bi bi-person-circle"></i>I tuoi dati</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="bk-label">Nome <span class="text-danger">*</span></label>
                                <input type="text" wire:model="customer_first_name" class="bk-input">
                                @error('customer_first_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="bk-label">Cognome <span class="text-danger">*</span></label>
                                <input type="text" wire:model="customer_last_name" class="bk-input">
                                @error('customer_last_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="bk-label">Email <span class="text-danger">*</span></label>
                                <input type="email" wire:model="customer_email" class="bk-input">
                                @error('customer_email') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="bk-label">Telefono</label>
                                <input type="tel" wire:model="customer_phone" class="bk-input">
                                @error('customer_phone') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-12">
                                <label class="bk-label">Richieste speciali</label>
                                <textarea wire:model="special_requests" class="bk-textarea" placeholder="Allergie, esigenze particolari, ecc."></textarea>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- RIGHT: summary --}}
            <div class="col-lg-4">
                <div class="bk-summary">
                    <div class="bk-card">
                        <h3><i class="bi bi-receipt"></i>Riepilogo</h3>

                        @if ($this->totalSelected === 0)
                            <p class="text-muted small mb-0">Seleziona almeno un partecipante per vedere il prezzo.</p>
                        @else
                            @foreach ($this->pricing['brackets'] ?? [] as $line)
                                <div class="bk-summary-line">
                                    <span>{{ $line['label'] }} × {{ $line['count'] }}</span>
                                    <span>€ {{ number_format($line['line_total'], 2, ',', '.') }}</span>
                                </div>
                            @endforeach

                            @if (($this->pricing['addons_total'] ?? 0) > 0)
                                <div class="bk-summary-line">
                                    <span>Servizi extra</span>
                                    <span>€ {{ number_format($this->pricing['addons_total'], 2, ',', '.') }}</span>
                                </div>
                            @endif

                            @if (($this->pricing['discount_amount'] ?? 0) > 0)
                                <div class="bk-summary-line discount">
                                    <span><i class="bi bi-tag-fill me-1"></i>Sconto</span>
                                    <span>− € {{ number_format($this->pricing['discount_amount'], 2, ',', '.') }}</span>
                                </div>
                            @endif

                            @if (($this->pricing['tax_amount'] ?? 0) > 0)
                                <div class="bk-summary-line text-muted">
                                    <span>IVA ({{ rtrim(rtrim(number_format($this->pricing['tax_rate'], 2), '0'), '.') }}%)</span>
                                    <span>€ {{ number_format($this->pricing['tax_amount'], 2, ',', '.') }}</span>
                                </div>
                            @endif

                            <div class="bk-summary-line total">
                                <span>Totale</span>
                                <span>€ {{ number_format($this->pricing['total_amount'], 2, ',', '.') }}</span>
                            </div>
                        @endif

                        {{-- Discount code --}}
                        <div class="mt-3 pt-3" style="border-top:1px solid #eef0f3;">
                            <label class="bk-label">Codice sconto</label>
                            <div class="d-flex gap-2">
                                <input type="text" wire:model="discountCode" class="bk-input" placeholder="ES. ESTATE10" @disabled($discountValid)>
                                @if ($discountValid)
                                    <button type="button" wire:click="removeDiscount" class="btn btn-light border rounded-pill px-3" title="Rimuovi"><i class="bi bi-x-lg"></i></button>
                                @else
                                    <button type="button" wire:click="applyDiscount" class="btn btn-outline-primary rounded-pill px-3">Applica</button>
                                @endif
                            </div>
                            @if ($discountFeedback)
                                <small class="d-block mt-1 {{ $discountValid ? 'text-success' : 'text-danger' }}">{{ $discountFeedback }}</small>
                            @endif
                        </div>

                        {{-- Terms --}}
                        @if ($departure)
                            <div class="form-check mt-3 pt-3" style="border-top:1px solid #eef0f3;">
                                <input class="form-check-input" type="checkbox" wire:model="terms" id="bk-terms">
                                <label class="form-check-label small" for="bk-terms">
                                    Accetto i <a href="{{ route('terms') }}" target="_blank">termini e condizioni</a> e la <a href="{{ route('privacy') }}" target="_blank">privacy policy</a>.
                                </label>
                                @error('terms') <small class="d-block text-danger">{{ $message }}</small> @enderror
                            </div>

                            @if ($errorMessage)
                                <div class="alert alert-danger mt-3 mb-0 small">{{ $errorMessage }}</div>
                            @endif

                            <button type="button" class="bk-submit mt-3" wire:click="submit" wire:loading.attr="disabled" @disabled($this->totalSelected === 0)>
                                <span wire:loading.remove wire:target="submit">
                                    <i class="bi bi-lock-fill me-2"></i>Procedi al pagamento
                                </span>
                                <span wire:loading wire:target="submit">
                                    <i class="bi bi-hourglass-split me-2"></i>Attendere...
                                </span>
                            </button>

                            <small class="d-block text-muted text-center mt-2" style="font-size:.78rem">
                                <i class="bi bi-shield-check me-1"></i>Pagamento sicuro via Stripe
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
