@extends('layouts.app')

@section('title', 'Privacy Policy - Solarya Travel')

@section('content')
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="mx-auto" style="max-width:780px">
                <h1 class="display-5 fw-bold text-navy mb-4 font-serif">Privacy Policy</h1>
                
                <div class="text-secondary">
                    <p class="lead text-secondary">
                        La presente Privacy Policy descrive le modalità di gestione del sito 
                        in riferimento al trattamento dei dati personali degli utenti.
                    </p>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">1. Titolare del Trattamento</h2>
                    <p>
                        Il Titolare del trattamento dei dati è Solarya Travel S.r.l., con sede legale in 
                        Via del Mare, 123 - 00100 Città di Mare (RM), P.IVA 01234567890.
                    </p>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">2. Dati Raccolti</h2>
                    <p>
                        I dati personali raccolti durante la navigazione sul sito e l'utilizzo dei servizi includono:
                    </p>
                    <ul class="ps-4">
                        <li>Dati identificativi (nome, cognome, codice fiscale)</li>
                        <li>Dati di contatto (email, telefono, indirizzo)</li>
                        <li>Dati di pagamento (trattati tramite gateway sicuri)</li>
                        <li>Dati di navigazione (indirizzo IP, browser, sistema operativo)</li>
                    </ul>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">3. Finalità del Trattamento</h2>
                    <p>I dati personali sono trattati per le seguenti finalità:</p>
                    <ul class="ps-4">
                        <li>Gestione delle prenotazioni e dei pagamenti</li>
                        <li>Comunicazioni relative ai servizi richiesti</li>
                        <li>Adempimento di obblighi legali e fiscali</li>
                        <li>Marketing e promozioni (previo consenso)</li>
                    </ul>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">4. Base Giuridica</h2>
                    <p>
                        Il trattamento dei dati personali si basa su:
                    </p>
                    <ul class="ps-4">
                        <li>Esecuzione di un contratto di cui l'interessato è parte</li>
                        <li>Adempimento di obblighi legali</li>
                        <li>Consenso dell'interessato per attività di marketing</li>
                        <li>Legittimo interesse del titolare</li>
                    </ul>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">5. Conservazione dei Dati</h2>
                    <p>
                        I dati personali sono conservati per il tempo strettamente necessario a conseguire 
                        le finalità per cui sono stati raccolti, nel rispetto dei termini di legge.
                    </p>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">6. Diritti dell'Interessato</h2>
                    <p>
                        In qualsiasi momento è possibile esercitare i diritti previsti dal GDPR:
                    </p>
                    <ul class="ps-4">
                        <li>Diritto di accesso ai dati personali</li>
                        <li>Diritto di rettifica dei dati inesatti</li>
                        <li>Diritto alla cancellazione ("diritto all'oblio")</li>
                        <li>Diritto alla limitazione del trattamento</li>
                        <li>Diritto alla portabilità dei dati</li>
                        <li>Diritto di opposizione</li>
                    </ul>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">7. Sicurezza</h2>
                    <p>
                        Adottiamo misure di sicurezza tecniche e organizzative adeguate per proteggere 
                        i dati personali da accessi non autorizzati, perdita o distruzione.
                    </p>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">8. Contatti</h2>
                    <p>
                        Per esercitare i propri diritti o per qualsiasi informazione relativa alla 
                        privacy, è possibile contattarci all'indirizzo: 
                        <a href="mailto:privacy@solaryatravel.it" class="text-primary">
                            privacy@solaryatravel.it
                        </a>
                    </p>

                    <p class="small text-muted mt-4">
                        Ultimo aggiornamento: {{ date('d/m/Y') }}
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
