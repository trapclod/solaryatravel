<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Domani parti!</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;color:#0f172a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 16px rgba(15,23,42,.06);">
                <tr>
                    <td style="background:linear-gradient(135deg,#10b981 0%,#0066cc 100%);padding:32px 28px;color:#ffffff;">
                        <div style="font-size:14px;letter-spacing:.08em;text-transform:uppercase;opacity:.85;">Domani si parte</div>
                        <div style="font-size:24px;font-weight:700;margin-top:4px;">🌊 Tutto pronto per il tuo tour?</div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:28px;">
                        <p style="margin:0 0 12px 0;font-size:16px;">Ciao <strong>{{ $booking->customer_first_name }}</strong>,</p>
                        <p style="margin:0 0 16px 0;line-height:1.6;">
                            domani parti! Ecco un riepilogo della tua prenotazione e dei partecipanti registrati.
                        </p>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:10px;padding:14px;margin:12px 0 20px 0;">
                            <tr>
                                <td style="padding:4px 0;font-size:13px;color:#64748b;width:140px;">Tour</td>
                                <td style="padding:4px 0;font-size:14px;"><strong>{{ $booking->tour->name ?? '' }}</strong></td>
                            </tr>
                            @if($booking->departure)
                            <tr>
                                <td style="padding:4px 0;font-size:13px;color:#64748b;">Data e partenza</td>
                                <td style="padding:4px 0;font-size:14px;"><strong>{{ \Carbon\Carbon::parse($booking->departure->departure_date)->locale('it')->isoFormat('dddd D MMMM') }} · {{ \Carbon\Carbon::parse($booking->departure->start_time)->format('H:i') }}</strong></td>
                            </tr>
                            @endif
                            <tr>
                                <td style="padding:4px 0;font-size:13px;color:#64748b;">Prenotazione</td>
                                <td style="padding:4px 0;font-size:14px;font-family:ui-monospace,Menlo,monospace;">#{{ $booking->booking_number }}</td>
                            </tr>
                        </table>

                        <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin:20px 0 10px;">Partecipanti registrati ({{ $booking->seatRecords->count() }})</h3>
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;">
                            @foreach($booking->seatRecords as $idx => $seat)
                                <tr style="{{ $idx > 0 ? 'border-top:1px solid #e2e8f0;' : '' }}">
                                    <td style="padding:10px 14px;font-size:13px;width:40px;color:#94a3b8;font-weight:700;">{{ $idx + 1 }}</td>
                                    <td style="padding:10px 14px;font-size:14px;">
                                        <strong>{{ trim(($seat->guest_first_name ?? '') . ' ' . ($seat->guest_last_name ?? '')) ?: '—' }}</strong>
                                        @if($seat->tax_code)
                                            <div style="font-size:12px;color:#64748b;font-family:ui-monospace,Menlo,monospace;margin-top:2px;">CF: {{ $seat->tax_code }}</div>
                                        @endif
                                    </td>
                                    <td style="padding:10px 14px;font-size:12px;color:#64748b;text-align:right;">
                                        @if($seat->ageBracket)
                                            {{ $seat->ageBracket->label }}
                                        @elseif($seat->is_primary)
                                            Prenotante
                                        @else
                                            Adulto
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>

                        <div style="margin:24px 0;padding:14px;background:#eff6ff;border-radius:10px;font-size:13px;color:#1e40af;line-height:1.6;">
                            <strong>📋 Cosa portare:</strong><br>
                            • Documento d'identità di ogni partecipante<br>
                            • Il biglietto QR (dalla mail con i biglietti)<br>
                            • Crema solare, costume, asciugamano
                        </div>

                        <div style="text-align:center;margin:24px 0;">
                            <a href="{{ route('booking.tickets', $booking->uuid) }}" style="display:inline-block;background:#0066cc;color:#fff;padding:12px 24px;border-radius:10px;text-decoration:none;font-weight:600;font-size:14px;">
                                Apri i biglietti QR
                            </a>
                        </div>

                        <p style="margin:16px 0 0 0;font-size:13px;color:#94a3b8;line-height:1.6;">
                            Buon viaggio,<br>
                            il team {{ config('app.name') }}
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:18px 28px;background:#f8fafc;border-top:1px solid #e2e8f0;text-align:center;font-size:12px;color:#94a3b8;">
                        © {{ now()->year }} {{ config('app.name') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
