@extends('layouts.admin')

@section('title', 'Check-in QR Scanner')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Header --}}
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-900">Check-in Passeggeri</h1>
            <p class="text-gray-600 mt-1">Scansiona il QR code della prenotazione o inserisci il numero manualmente</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- QR Scanner --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    Scanner QR Code
                </h2>

                <div id="scanner-container" class="relative">
                    <div id="reader" class="w-full aspect-square bg-gray-900 rounded-lg overflow-hidden"></div>
                    <div id="scanner-placeholder" class="absolute inset-0 flex items-center justify-center bg-gray-100 rounded-lg">
                        <div class="text-center">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <p class="text-gray-500 mb-4">Clicca per attivare la fotocamera</p>
                            <button id="start-scanner" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                                Attiva Scanner
                            </button>
                        </div>
                    </div>
                </div>

                <div id="scanner-status" class="mt-4 text-center text-sm text-gray-500">
                    Scanner pronto
                </div>
            </div>

            {{-- Manual Entry --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Inserimento Manuale
                </h2>

                <form id="manual-checkin-form" action="{{ route('admin.checkin.manual') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="booking_number" class="block text-sm font-medium text-gray-700 mb-1">
                                Numero Prenotazione
                            </label>
                            <input type="text" 
                                   name="booking_number" 
                                   id="booking_number" 
                                   placeholder="es. SOL-2026-XXXX"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-lg text-lg font-mono focus:ring-2 focus:ring-primary-500 focus:border-primary-500 uppercase">
                        </div>

                        <div>
                            <label for="verification_code" class="block text-sm font-medium text-gray-700 mb-1">
                                Codice Verifica (opzionale)
                            </label>
                            <input type="text" 
                                   name="verification_code" 
                                   id="verification_code" 
                                   placeholder="Codice a 6 cifre"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-lg text-lg font-mono focus:ring-2 focus:ring-primary-500 focus:border-primary-500 uppercase"
                                   maxlength="6">
                        </div>

                        <button type="submit" class="w-full py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                            Cerca Prenotazione
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Result Panel --}}
        <div id="result-panel" class="hidden bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div id="result-header" class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-semibold">Risultato Check-in</h3>
            </div>
            <div id="result-content" class="p-6">
                {{-- Dynamic content will be inserted here --}}
            </div>
        </div>

        {{-- Today's Check-ins --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Check-in di Oggi</h3>
                <span class="text-sm text-gray-500" id="today-date">{{ now()->format('d/m/Y') }}</span>
            </div>
            <div id="today-checkins" class="divide-y divide-gray-100">
                <div class="px-6 py-8 text-center text-gray-500">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <p>Nessun check-in effettuato oggi</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.4/html5-qrcode.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startButton = document.getElementById('start-scanner');
        const placeholder = document.getElementById('scanner-placeholder');
        const statusEl = document.getElementById('scanner-status');
        const resultPanel = document.getElementById('result-panel');
        const resultContent = document.getElementById('result-content');
        let html5QrCode = null;

        // Start scanner
        startButton.addEventListener('click', function() {
            placeholder.classList.add('hidden');
            
            html5QrCode = new Html5Qrcode("reader");
            
            html5QrCode.start(
                { facingMode: "environment" },
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                console.error('Camera error:', err);
                statusEl.textContent = 'Errore accesso fotocamera';
                statusEl.classList.add('text-red-500');
                placeholder.classList.remove('hidden');
            });
        });

        function onScanSuccess(decodedText, decodedResult) {
            // Vibrate if supported
            if (navigator.vibrate) {
                navigator.vibrate(200);
            }

            statusEl.textContent = 'QR Code rilevato!';
            statusEl.classList.remove('text-gray-500');
            statusEl.classList.add('text-green-600');

            // Process the QR code
            processQRCode(decodedText);
        }

        function onScanFailure(error) {
            // Ignore scan failures
        }

        function processQRCode(qrCode) {
            fetch('{{ route('admin.checkin.verify') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ qr_code: qrCode })
            })
            .then(response => response.json())
            .then(data => {
                showResult(data);
            })
            .catch(error => {
                showResult({ success: false, message: 'Errore di connessione' });
            });
        }

        // Manual form submission
        document.getElementById('manual-checkin-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                showResult(data);
            })
            .catch(error => {
                showResult({ success: false, message: 'Errore di connessione' });
            });
        });

        function showResult(data) {
            resultPanel.classList.remove('hidden');
            
            if (data.success) {
                resultContent.innerHTML = `
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-green-600 mb-2">Check-in Completato!</h4>
                        <p class="text-gray-600">${data.message || 'Prenotazione verificata con successo'}</p>
                        ${data.booking ? `
                            <div class="mt-4 p-4 bg-gray-50 rounded-lg text-left">
                                <p><strong>Prenotazione:</strong> ${data.booking.booking_number}</p>
                                <p><strong>Cliente:</strong> ${data.booking.customer_name}</p>
                                <p><strong>Ospiti:</strong> ${data.booking.seats}</p>
                                <p><strong>Catamarano:</strong> ${data.booking.catamaran}</p>
                            </div>
                        ` : ''}
                    </div>
                `;
            } else {
                resultContent.innerHTML = `
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <h4 class="text-xl font-bold text-red-600 mb-2">Errore</h4>
                        <p class="text-gray-600">${data.message || 'Si è verificato un errore'}</p>
                    </div>
                `;
            }

            // Scroll to result
            resultPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });

            // Reset status after delay
            setTimeout(() => {
                statusEl.textContent = 'Scanner pronto';
                statusEl.classList.remove('text-green-600', 'text-red-500');
                statusEl.classList.add('text-gray-500');
            }, 3000);
        }
    });
</script>
@endpush
