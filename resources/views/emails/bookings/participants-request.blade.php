<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Completa i dati dei partecipanti</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;color:#0f172a;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:24px 0;">
    <tr>
        <td align="center">
            <table role="presentation" width="640" cellpadding="0" cellspacing="0" style="max-width:640px;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 16px rgba(15,23,42,.06);">
                <tr>
                    <td style="background:linear-gradient(135deg,#0066cc 0%,#0ea5e9 100%);padding:32px 28px;color:#ffffff;">
                        <div style="font-size:14px;letter-spacing:.08em;text-transform:uppercase;opacity:.85;">{{ config('app.name') }}</div>
                        <div style="font-size:22px;font-weight:700;margin-top:4px;">Manca solo un passaggio</div>
                    </td>
                </tr>

                <tr>
                    <td style="padding:28px;">
                        <p style="margin:0 0 12px 0;font-size:16px;">Ciao <strong>{{ $booking->customer_first_name }}</strong>,</p>
                        <p style="margin:0 0 16px 0;line-height:1.6;">
                            grazie per aver completato il pagamento della prenotazione <strong>#{{ $booking->booking_number }}</strong>.
                            Per legge dobbiamo registrare i dati di tutti i passeggeri prima dell'imbarco.
                        </p>
                        <p style="margin:0 0 16px 0;line-height:1.6;">
                            Compila <strong>nome, cognome e codice fiscale</strong> di ciascun partecipante (per i bambini la data di nascita
                            è già salvata): ti basta un minuto.
                        </p>

                        <div style="margin:24px 0;text-align:center;">
                            <a href="{{ $url }}" style="display:inline-block;background:#0066cc;color:#fff;padding:14px 28px;border-radius:10px;text-decoration:none;font-weight:600;font-size:15px;">
                                Compila i dati partecipanti
                            </a>
                        </div>

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
                                <td style="padding:4px 0;font-size:13px;color:#64748b;">Partecipanti</td>
                                <td style="padding:4px 0;font-size:14px;">{{ $booking->seatRecords()->count() }} posti</td>
                            </tr>
                        </table>

                        <p style="margin:0 0 8px 0;font-size:13px;color:#64748b;line-height:1.6;">
                            ⚠️ Senza i dati dei partecipanti l'imbarco non potrà essere effettuato. Ricordati di completarli prima della partenza.
                        </p>

                        <p style="margin:16px 0 0 0;font-size:13px;color:#94a3b8;line-height:1.6;">
                            Se il link sopra non funziona, copia questo nel browser:<br>
                            <span style="word-break:break-all;font-family:ui-monospace,Menlo,monospace;">{{ $url }}</span>
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
