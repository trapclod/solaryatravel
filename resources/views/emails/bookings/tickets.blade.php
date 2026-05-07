<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>I tuoi biglietti</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;color:#0f172a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 16px rgba(15,23,42,.06);">
                <tr>
                    <td style="background:linear-gradient(135deg,#10b981 0%,#0066cc 100%);padding:32px 28px;color:#ffffff;">
                        <div style="font-size:14px;letter-spacing:.08em;text-transform:uppercase;opacity:.85;">{{ config('app.name') }}</div>
                        <div style="font-size:24px;font-weight:700;margin-top:4px;">Pagamento confermato — ecco i tuoi biglietti!</div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:28px;">
                        <p style="margin:0 0 12px 0;font-size:16px;">Ciao <strong>{{ $booking->customer_first_name }}</strong>,</p>
                        <p style="margin:0 0 16px 0;line-height:1.6;">
                            il pagamento è andato a buon fine. Qui sotto trovi <strong>un biglietto per ogni passeggero</strong>:
                            mostralo al personale (da stampa o dal cellulare) al momento dell'imbarco. Verrà scansionato il QR code per registrare la tua presenza.
                        </p>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border-radius:10px;padding:14px;margin:12px 0 20px 0;">
                            <tr>
                                <td style="padding:4px 0;font-size:13px;color:#64748b;width:140px;">Tour</td>
                                <td style="padding:4px 0;font-size:14px;"><strong>{{ $booking->tour->name ?? '' }}</strong></td>
                            </tr>
                            @if($booking->departure)
                            <tr>
                                <td style="padding:4px 0;font-size:13px;color:#64748b;">Data e partenza</td>
                                <td style="padding:4px 0;font-size:14px;">{{ \Carbon\Carbon::parse($booking->departure->departure_date)->format('d/m/Y') }} · {{ \Carbon\Carbon::parse($booking->departure->start_time)->format('H:i') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td style="padding:4px 0;font-size:13px;color:#64748b;">Prenotazione</td>
                                <td style="padding:4px 0;font-size:14px;font-family:ui-monospace,Menlo,monospace;">#{{ $booking->booking_number }}</td>
                            </tr>
                        </table>

                        @foreach($tickets as $t)
                            @php($seat = $t['seat'])
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:2px dashed #0066cc;border-radius:14px;margin-bottom:16px;background:linear-gradient(135deg,#ffffff 0%,#f7faff 100%);">
                                <tr>
                                    <td style="padding:18px;width:55%;vertical-align:top;">
                                        <div style="font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#64748b;font-weight:600;">Passeggero</div>
                                        <div style="font-size:20px;font-weight:800;color:#0066cc;margin-top:2px;">
                                            @if($seat->is_primary)<span style="color:#f59e0b;">★</span>@endif
                                            {{ $seat->guest_full_name ?: $booking->customer_full_name }}
                                        </div>
                                        @if($seat->ageBracket)
                                            <div style="display:inline-block;background:#fff;border:1px solid #e5e7eb;border-radius:6px;padding:2px 8px;font-size:12px;color:#475569;margin-top:6px;">
                                                {{ $seat->ageBracket->label }}
                                            </div>
                                        @endif

                                        <div style="border-top:1px solid #e5e7eb;margin:14px 0;"></div>

                                        <div style="font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#64748b;font-weight:600;">Posto</div>
                                        <div style="font-size:14px;font-weight:700;color:#0f172a;">#{{ $seat->seat_number ?? $loop->iteration }}</div>

                                        @if($seat->catamaran)
                                            <div style="font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#64748b;font-weight:600;margin-top:10px;">Catamarano</div>
                                            <div style="font-size:14px;font-weight:700;color:#0f172a;">{{ $seat->catamaran->name }}</div>
                                        @endif
                                    </td>
                                    <td style="padding:18px;width:45%;text-align:center;border-left:2px dashed #cbd5e1;vertical-align:middle;">
                                        <img src="{{ $t['qr_data'] }}" alt="QR biglietto" style="display:block;margin:0 auto;width:180px;height:180px;background:#fff;padding:6px;border-radius:6px;">
                                        <div style="font-family:ui-monospace,Menlo,monospace;font-size:11px;color:#475569;margin-top:8px;">{{ $seat->qr_code }}</div>
                                    </td>
                                </tr>
                            </table>
                        @endforeach

                        <div style="background:#fef3c7;border-left:4px solid #f59e0b;padding:14px;border-radius:6px;margin-top:18px;">
                            <p style="margin:0;font-size:13px;color:#78350f;line-height:1.6;">
                                <strong>Suggerimento:</strong> presentati al molo almeno 15 minuti prima della partenza con il QR code di ogni passeggero (anche dal cellulare).
                            </p>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td style="background:#f8fafc;padding:18px 28px;border-top:1px solid #e5e7eb;font-size:12px;color:#64748b;text-align:center;">
                        Buon viaggio!<br>
                        © {{ date('Y') }} {{ config('app.name') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
