<div class="participants-form">
    <style>
        .participants-form { max-width: 760px; margin: 0 auto; }
        .pf-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 16px;
        }
        .pf-header { margin-bottom: 22px; }
        .pf-header h1 { font-size: 1.5rem; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
        .pf-header p { color: #64748b; margin: 0; font-size: .95rem; }
        .pf-seat {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 14px;
            background: #fff;
        }
        .pf-seat-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }
        .pf-seat-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: #0066cc;
            color: #fff;
            border-radius: 50%;
            font-weight: 700;
            font-size: .9rem;
            margin-right: 10px;
        }
        .pf-seat-title { font-weight: 700; color: #0f172a; font-size: 1rem; }
        .pf-seat-meta { font-size: .82rem; color: #64748b; font-weight: 500; }
        .pf-tag {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .pf-tag-primary { background: #dbeafe; color: #1d4ed8; }
        .pf-tag-child { background: #fef3c7; color: #b45309; }
        .pf-tag-adult { background: #ecfdf5; color: #059669; }
        .pf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }
        @media (max-width: 575.98px) { .pf-row { grid-template-columns: 1fr; } }
        .pf-label { display: block; font-size: .8rem; font-weight: 600; color: #475569; margin-bottom: 5px; }
        .pf-input {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 9px 12px;
            font-size: .95rem;
            background: #fff;
            transition: border-color .15s, box-shadow .15s;
        }
        .pf-input:focus { outline: none; border-color: #0066cc; box-shadow: 0 0 0 3px rgba(0,102,204,.15); }
        .pf-input:disabled { background: #f8fafc; color: #94a3b8; }
        .pf-error { color: #dc2626; font-size: .8rem; margin-top: 4px; display: block; }
        .pf-submit {
            background: #0066cc;
            color: #fff;
            border: none;
            padding: 12px 28px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all .15s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .pf-submit:hover { background: #0052a3; }
        .pf-submit:disabled { opacity: .5; cursor: not-allowed; }
        .pf-success {
            background: #ecfdf5;
            border: 1px solid #6ee7b7;
            color: #065f46;
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-weight: 600;
        }
        .pf-hint { font-size: .82rem; color: #64748b; }
    </style>

    <div class="pf-header">
        <h1>Dati dei partecipanti</h1>
        <p>Prenotazione <strong>#{{ $booking->booking_number }}</strong> · {{ $booking->tour->name ?? '' }}
            @if($booking->departure) · {{ \Carbon\Carbon::parse($booking->departure->departure_date)->format('d/m/Y') }} ore {{ \Carbon\Carbon::parse($booking->departure->start_time)->format('H:i') }} @endif
        </p>
    </div>

    @if($successMessage)
        <div class="pf-success">
            <i class="fa-solid fa-circle-check me-2"></i>{{ $successMessage }}
        </div>
    @endif

    <div class="pf-card">
        <p class="pf-hint mb-3">
            <i class="fa-solid fa-circle-info me-1"></i>
            Compila <strong>nome, cognome e codice fiscale</strong> di ogni partecipante. I dati sono richiesti per legge prima dell'imbarco.
            Per i bambini la data di nascita è già stata salvata in fase di prenotazione.
        </p>

        <form wire:submit="save">
            @foreach($seats as $idx => $seat)
                <div class="pf-seat" wire:key="seat-{{ $seat['id'] }}">
                    <div class="pf-seat-head">
                        <div>
                            <span class="pf-seat-num">{{ $idx + 1 }}</span>
                            <span class="pf-seat-title">Partecipante {{ $idx + 1 }}</span>
                            <span class="pf-seat-meta d-block ms-5 ps-2">{{ $seat['label'] }}</span>
                        </div>
                        <div>
                            @if($seat['is_primary'])
                                <span class="pf-tag pf-tag-primary">Prenotante</span>
                            @elseif($seat['is_child'])
                                <span class="pf-tag pf-tag-child">Bambino</span>
                            @else
                                <span class="pf-tag pf-tag-adult">Adulto</span>
                            @endif
                        </div>
                    </div>

                    <div class="pf-row">
                        <div>
                            <label class="pf-label">Nome *</label>
                            <input type="text" class="pf-input" wire:model.defer="seats.{{ $idx }}.first_name" placeholder="Mario">
                            @error("seats.{$idx}.first_name") <span class="pf-error">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="pf-label">Cognome *</label>
                            <input type="text" class="pf-input" wire:model.defer="seats.{{ $idx }}.last_name" placeholder="Rossi">
                            @error("seats.{$idx}.last_name") <span class="pf-error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="pf-row">
                        <div>
                            <label class="pf-label">Codice fiscale *</label>
                            <input type="text" class="pf-input text-uppercase" wire:model.defer="seats.{{ $idx }}.tax_code" placeholder="RSSMRA80A01H501Z" maxlength="16">
                            @error("seats.{$idx}.tax_code") <span class="pf-error">{{ $message }}</span> @enderror
                        </div>
                        @if($seat['dob'])
                            <div>
                                <label class="pf-label">Data di nascita</label>
                                <input type="date" class="pf-input" value="{{ $seat['dob'] }}" disabled>
                                <span class="pf-hint d-block mt-1"><i class="fa-solid fa-lock me-1"></i>Salvata in fase di prenotazione</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <div class="text-center mt-3">
                <button type="submit" class="pf-submit" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save"><i class="fa-solid fa-floppy-disk me-2"></i>Salva i dati</span>
                    <span wire:loading wire:target="save"><i class="fa-solid fa-spinner fa-spin me-2"></i>Salvataggio…</span>
                </button>
            </div>
        </form>
    </div>
</div>
