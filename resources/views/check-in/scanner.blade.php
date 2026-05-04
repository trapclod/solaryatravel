@extends('layouts.admin')

@section('title', 'Check-in QR Scanner')

@section('content')
    {{-- Page header --}}
    <div class="dash-page-header">
        <div>
            <h1>Check-in passeggeri</h1>
            <p>Scansiona il QR code della prenotazione o inserisci il numero manualmente.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="badge bg-light text-secondary fw-medium border px-3 py-2 rounded-pill">
                <i class="bi bi-calendar3 me-2"></i>{{ now()->locale('it')->isoFormat('dddd, D MMMM YYYY') }}
            </span>
            <a href="{{ route('admin.bookings.index', ['date_from' => today()->format('Y-m-d'), 'date_to' => today()->format('Y-m-d')]) }}"
               class="btn btn-light rounded-pill px-3 fw-semibold border">
                <i class="bi bi-list-ul me-2"></i>Prenotazioni di oggi
            </a>
        </div>
    </div>

    {{-- Mini stats --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <span class="mini-stat-icon bg-success-subtle text-success"><i class="bi bi-check2-circle"></i></span>
                <div>
                    <div class="mini-stat-value" id="stat-success">0</div>
                    <div class="mini-stat-label">Check-in OK</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <span class="mini-stat-icon bg-danger-subtle text-danger"><i class="bi bi-x-octagon"></i></span>
                <div>
                    <div class="mini-stat-value" id="stat-errors">0</div>
                    <div class="mini-stat-label">Errori</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <span class="mini-stat-icon bg-info-subtle text-info"><i class="bi bi-camera-video"></i></span>
                <div>
                    <div class="mini-stat-value" id="stat-status" style="font-size:1rem">Pronto</div>
                    <div class="mini-stat-label">Scanner</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="dash-mini-stat">
                <span class="mini-stat-icon bg-warning-subtle text-warning"><i class="bi bi-clock-history"></i></span>
                <div>
                    <div class="mini-stat-value" id="stat-last" style="font-size:1rem">—</div>
                    <div class="mini-stat-label">Ultimo check-in</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        {{-- QR Scanner --}}
        <div class="col-lg-7">
            <div class="dash-card h-100">
                <div class="dash-card-header">
                    <h3><i class="bi bi-qr-code-scan me-2 text-primary"></i>Scanner QR Code</h3>
                    <button id="stop-scanner" type="button" class="btn btn-sm btn-light rounded-pill border d-none">
                        <i class="bi bi-stop-circle me-1"></i>Stop
                    </button>
                </div>
                <div class="dash-card-body">
                    <div id="scanner-container" class="position-relative">
                        <div id="reader" class="ratio ratio-16x9 rounded-3 overflow-hidden" style="background:#0f172a"></div>
                        <div id="scanner-placeholder"
                             class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                             style="background: linear-gradient(135deg, #f8fafc 0%, #e0f2fe 100%); border-radius:.75rem">
                            <div class="text-center p-4">
                                <div class="mx-auto mb-3 rounded-circle bg-white shadow-sm d-inline-flex align-items-center justify-content-center" style="width:72px;height:72px">
                                    <i class="bi bi-camera fs-2 text-primary"></i>
                                </div>
                                <h4 class="h6 fw-bold mb-2">Pronto per la scansione</h4>
                                <p class="text-muted small mb-3">Attiva la fotocamera per leggere il QR code della prenotazione.</p>
                                <button id="start-scanner" type="button" class="btn btn-primary rounded-pill px-4 fw-semibold">
                                    <i class="bi bi-play-fill me-2"></i>Attiva scanner
                                </button>
                            </div>
                        </div>

                        {{-- Scan frame overlay --}}
                        <div id="scan-frame" class="position-absolute top-50 start-50 translate-middle d-none pointer-events-none">
                            <div class="scan-corner scan-tl"></div>
                            <div class="scan-corner scan-tr"></div>
                            <div class="scan-corner scan-bl"></div>
                            <div class="scan-corner scan-br"></div>
                        </div>
                    </div>

                    <div id="scanner-status" class="mt-3 text-center small text-muted">
                        <i class="bi bi-circle-fill text-muted me-1" style="font-size:.5rem"></i>Scanner pronto
                    </div>
                </div>
            </div>
        </div>

        {{-- Manual Entry --}}
        <div class="col-lg-5">
            <div class="dash-card h-100">
                <div class="dash-card-header">
                    <h3><i class="bi bi-pencil-square me-2 text-primary"></i>Inserimento manuale</h3>
                </div>
                <div class="dash-card-body">
                    <p class="text-muted small mb-3">
                        Se il QR code non è leggibile, inserisci manualmente il numero della prenotazione.
                    </p>
                    <form id="manual-checkin-form" action="{{ route('admin.checkin.manual') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="booking_number" class="form-label fw-semibold small text-uppercase text-muted" style="letter-spacing:.04em">
                                Numero prenotazione
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-hash"></i></span>
                                <input type="text" name="booking_number" id="booking_number"
                                       placeholder="SOL-2026-XXXX" required
                                       class="form-control form-control-lg font-monospace text-uppercase">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="verification_code" class="form-label fw-semibold small text-uppercase text-muted" style="letter-spacing:.04em">
                                Codice verifica <span class="text-muted text-lowercase fw-normal">(opzionale)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-shield-lock"></i></span>
                                <input type="text" name="verification_code" id="verification_code"
                                       placeholder="Codice di sicurezza" maxlength="16"
                                       class="form-control form-control-lg font-monospace text-uppercase">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-semibold">
                            <i class="bi bi-search me-2"></i>Verifica e check-in
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Result Panel --}}
    <div id="result-panel" class="dash-card mb-3 d-none">
        <div class="dash-card-header">
            <h3 id="result-title"><i class="bi bi-clipboard-check me-2 text-primary"></i>Risultato check-in</h3>
            <button type="button" class="btn-close" id="close-result" aria-label="Chiudi"></button>
        </div>
        <div id="result-content" class="dash-card-body"></div>
    </div>

    {{-- Today's Check-ins log --}}
    <div class="dash-card mb-4">
        <div class="dash-card-header">
            <h3><i class="bi bi-clock-history me-2 text-primary"></i>Cronologia di sessione</h3>
            <button type="button" class="btn btn-sm btn-light rounded-pill border" id="clear-log">
                <i class="bi bi-trash me-1"></i>Pulisci
            </button>
        </div>
        <div id="today-checkins" class="dash-card-body py-0">
            <div class="text-center py-5 text-muted" id="empty-log">
                <i class="bi bi-calendar-check display-5 opacity-50 d-block mb-2"></i>
                <p class="mb-1 fw-semibold">Nessun check-in in questa sessione</p>
                <p class="small mb-0">I check-in effettuati appariranno qui in tempo reale.</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .scan-corner {
        position: absolute;
        width: 32px;
        height: 32px;
        border: 3px solid #0284c7;
        animation: pulse-corner 2s ease-in-out infinite;
    }
    .scan-tl { top: -2px; left: -2px;    border-right: 0; border-bottom: 0; border-top-left-radius: .35rem; }
    .scan-tr { top: -2px; right: -2px;   border-left: 0;  border-bottom: 0; border-top-right-radius: .35rem; }
    .scan-bl { bottom: -2px; left: -2px; border-right: 0; border-top: 0;    border-bottom-left-radius: .35rem; }
    .scan-br { bottom: -2px; right: -2px;border-left: 0;  border-top: 0;    border-bottom-right-radius: .35rem; }
    #scan-frame { width: 220px; height: 220px; }
    @keyframes pulse-corner { 0%,100% { opacity: 1 } 50% { opacity: .4 } }

    #reader video { object-fit: cover; }

    .checkin-log-item {
        display: flex;
        align-items: center;
        gap: .85rem;
        padding: .85rem 0;
        border-bottom: 1px solid rgba(15,23,42,.05);
    }
    .checkin-log-item:last-child { border-bottom: 0; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.4/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startButton = document.getElementById('start-scanner');
        const stopButton = document.getElementById('stop-scanner');
        const placeholder = document.getElementById('scanner-placeholder');
        const scanFrame = document.getElementById('scan-frame');
        const statusEl = document.getElementById('scanner-status');
        const resultPanel = document.getElementById('result-panel');
        const resultContent = document.getElementById('result-content');
        const resultTitle = document.getElementById('result-title');
        const log = document.getElementById('today-checkins');
        const emptyLog = document.getElementById('empty-log');

        let html5QrCode = null;
        let counts = { success: 0, errors: 0 };

        function setStatus(text, kind = 'muted') {
            const colors = { muted: 'text-muted', success: 'text-success', danger: 'text-danger', info: 'text-info' };
            statusEl.className = 'mt-3 text-center small ' + colors[kind];
            statusEl.innerHTML = `<i class="bi bi-circle-fill ${colors[kind]} me-1" style="font-size:.5rem"></i>${text}`;
            document.getElementById('stat-status').textContent = text;
        }

        startButton.addEventListener('click', function () {
            placeholder.classList.add('d-none');
            scanFrame.classList.remove('d-none');
            stopButton.classList.remove('d-none');
            html5QrCode = new Html5Qrcode("reader");
            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess,
                () => {}
            ).then(() => {
                setStatus('Scansione in corso…', 'info');
            }).catch(err => {
                console.error('Camera error:', err);
                setStatus('Errore accesso fotocamera', 'danger');
                placeholder.classList.remove('d-none');
                scanFrame.classList.add('d-none');
                stopButton.classList.add('d-none');
            });
        });

        stopButton.addEventListener('click', stopScanner);

        function stopScanner() {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    placeholder.classList.remove('d-none');
                    scanFrame.classList.add('d-none');
                    stopButton.classList.add('d-none');
                    setStatus('Scanner fermato', 'muted');
                });
            }
        }

        function onScanSuccess(decodedText) {
            if (navigator.vibrate) navigator.vibrate(200);
            setStatus('QR code rilevato!', 'success');
            stopScanner();
            processQRCode(decodedText);
        }

        function processQRCode(qrCode) {
            fetch('{{ route('admin.checkin.verify') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ qr_code: qrCode })
            })
            .then(r => r.json()).then(showResult)
            .catch(() => showResult({ success: false, message: 'Errore di connessione' }));
        }

        document.getElementById('manual-checkin-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch(this.action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: formData
            })
            .then(r => r.json()).then(data => { showResult(data); if (data.success) this.reset(); })
            .catch(() => showResult({ success: false, message: 'Errore di connessione' }));
        });

        document.getElementById('close-result').addEventListener('click', () => {
            resultPanel.classList.add('d-none');
        });

        document.getElementById('clear-log').addEventListener('click', () => {
            log.querySelectorAll('.checkin-log-item').forEach(n => n.remove());
            emptyLog.classList.remove('d-none');
        });

        function showResult(data) {
            resultPanel.classList.remove('d-none');

            if (data.success) {
                counts.success++;
                document.getElementById('stat-success').textContent = counts.success;
                const time = new Date().toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit' });
                document.getElementById('stat-last').textContent = time;
                resultTitle.innerHTML = '<i class="bi bi-check-circle-fill me-2 text-success"></i>Check-in completato';

                const b = data.booking || {};
                resultContent.innerHTML = `
                    <div class="text-center mb-3">
                        <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center mb-3" style="width:72px;height:72px;">
                            <i class="bi bi-check-lg" style="font-size:2.25rem"></i>
                        </div>
                        <h4 class="fw-bold text-success mb-1">${data.message || 'Prenotazione verificata con successo'}</h4>
                        <p class="text-muted small mb-0">Il passeggero può procedere all'imbarco</p>
                    </div>
                    ${b.booking_number ? `
                        <div class="bg-light rounded-3 p-3 mt-3">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="small text-muted text-uppercase fw-semibold mb-1" style="letter-spacing:.04em">Prenotazione</div>
                                    <div class="font-monospace fw-bold">#${b.booking_number}</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="small text-muted text-uppercase fw-semibold mb-1" style="letter-spacing:.04em">Cliente</div>
                                    <div class="fw-semibold">${b.customer_name ?? '—'}</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="small text-muted text-uppercase fw-semibold mb-1" style="letter-spacing:.04em">Catamarano</div>
                                    <div><i class="bi bi-water text-primary me-1"></i>${b.catamaran ?? '—'}</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="small text-muted text-uppercase fw-semibold mb-1" style="letter-spacing:.04em">Ospiti</div>
                                    <div><i class="bi bi-people text-muted me-1"></i>${b.seats ?? '—'}</div>
                                </div>
                            </div>
                        </div>` : ''}
                `;

                appendToLog({
                    success: true,
                    name: b.customer_name || 'Prenotazione',
                    detail: `#${b.booking_number || '—'} · ${b.catamaran || ''}`,
                    time
                });
            } else {
                counts.errors++;
                document.getElementById('stat-errors').textContent = counts.errors;
                resultTitle.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>Errore check-in';
                resultContent.innerHTML = `
                    <div class="text-center">
                        <div class="rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center mb-3" style="width:72px;height:72px;">
                            <i class="bi bi-x-lg" style="font-size:2.25rem"></i>
                        </div>
                        <h4 class="fw-bold text-danger mb-1">Operazione non riuscita</h4>
                        <p class="text-muted">${data.message || 'Si è verificato un errore'}</p>
                    </div>
                `;

                const time = new Date().toLocaleTimeString('it-IT', { hour: '2-digit', minute: '2-digit' });
                appendToLog({
                    success: false,
                    name: 'Tentativo fallito',
                    detail: data.message || 'Errore sconosciuto',
                    time
                });
            }

            resultPanel.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(() => setStatus('Scanner pronto', 'muted'), 3000);
        }

        function appendToLog(item) {
            emptyLog.classList.add('d-none');
            const cls = item.success ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger';
            const icon = item.success ? 'bi-check-lg' : 'bi-x-lg';
            const node = document.createElement('div');
            node.className = 'checkin-log-item';
            node.innerHTML = `
                <span class="avatar-sm ${cls}"><i class="bi ${icon}"></i></span>
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-semibold text-truncate">${item.name}</div>
                    <div class="small text-muted text-truncate">${item.detail}</div>
                </div>
                <span class="small text-muted text-nowrap">${item.time}</span>
            `;
            log.insertBefore(node, log.firstChild);
        }
    });
</script>
@endpush
