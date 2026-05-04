@extends('layouts.app')

@section('title', 'Termini e Condizioni - Solarya Travel')

@section('content')
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="mx-auto" style="max-width:780px">
                <h1 class="display-5 fw-bold text-navy mb-4 font-serif">Termini e Condizioni</h1>
                
                <div class="text-secondary">
                    <p class="lead text-secondary">
                        I presenti Termini e Condizioni regolano l'utilizzo del sito web e dei servizi 
                        offerti da Solarya Travel S.r.l.
                    </p>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">1. Accettazione dei Termini</h2>
                    <p>
                        Utilizzando il sito e i servizi di Solarya Travel, l'utente accetta integralmente 
                        i presenti Termini e Condizioni. Se non si accettano tali condizioni, si prega 
                        di non utilizzare il sito.
                    </p>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">2. Prenotazioni</h2>
                    <p>
                        La prenotazione di un'escursione è vincolante una volta completato il pagamento. 
                        Al momento della prenotazione, l'utente riceverà una conferma via email con tutti 
                        i dettagli dell'esperienza.
                    </p>
                    <ul class="ps-4">
                        <li>È necessario presentarsi al punto di imbarco almeno 30 minuti prima della partenza</li>
                        <li>È obbligatorio presentare la conferma di prenotazione (digitale o stampata)</li>
                        <li>I documenti di identità devono essere validi</li>
                    </ul>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">3. Prezzi e Pagamenti</h2>
                    <p>
                        I prezzi indicati sul sito sono comprensivi di IVA e di tutti i servizi descritti. 
                        I pagamenti sono elaborati tramite gateway sicuri e certificati.
                    </p>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">4. Cancellazioni e Rimborsi</h2>
                    <ul class="ps-4">
                        <li><strong>Cancellazione fino a 7 giorni prima:</strong> Rimborso totale</li>
                        <li><strong>Cancellazione da 7 a 3 giorni prima:</strong> Rimborso del 50%</li>
                        <li><strong>Cancellazione meno di 3 giorni prima:</strong> Nessun rimborso</li>
                        <li><strong>Mancata presentazione:</strong> Nessun rimborso</li>
                    </ul>
                    <p class="mt-4">
                        In caso di cancellazione per maltempo da parte di Solarya Travel, 
                        sarà offerta una data alternativa o il rimborso completo.
                    </p>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">5. Responsabilità</h2>
                    <p>
                        Solarya Travel adotta tutte le misure necessarie per garantire la sicurezza degli ospiti. 
                        L'utente è tenuto a seguire le istruzioni dell'equipaggio e a comportarsi in modo 
                        responsabile durante l'escursione.
                    </p>
                    <p>
                        Solarya Travel non è responsabile per:
                    </p>
                    <ul class="ps-4">
                        <li>Oggetti personali smarriti o danneggiati</li>
                        <li>Danni derivanti dal mancato rispetto delle istruzioni di sicurezza</li>
                        <li>Eventi di forza maggiore (condizioni meteo, emergenze sanitarie, ecc.)</li>
                    </ul>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">6. Requisiti per la Partecipazione</h2>
                    <ul class="ps-4">
                        <li>I minori devono essere accompagnati da un adulto responsabile</li>
                        <li>È consigliato saper nuotare per le attività in acqua</li>
                        <li>In caso di condizioni di salute particolari, informare preventivamente l'equipaggio</li>
                        <li>Non è consentito l'imbarco in stato di ebbrezza</li>
                    </ul>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">7. Proprietà Intellettuale</h2>
                    <p>
                        Tutti i contenuti del sito (testi, immagini, loghi, grafiche) sono di proprietà 
                        di Solarya Travel e sono protetti dalle leggi sul diritto d'autore.
                    </p>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">8. Modifiche ai Termini</h2>
                    <p>
                        Solarya Travel si riserva il diritto di modificare i presenti Termini e Condizioni 
                        in qualsiasi momento. Le modifiche entreranno in vigore dalla pubblicazione sul sito.
                    </p>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">9. Legge Applicabile</h2>
                    <p>
                        I presenti Termini e Condizioni sono regolati dalla legge italiana. 
                        Per qualsiasi controversia sarà competente il Foro di Roma.
                    </p>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">10. Contatti</h2>
                    <p>
                        Per qualsiasi domanda sui presenti Termini e Condizioni, contattaci a: 
                        <a href="mailto:info@solaryatravel.it" class="text-primary">
                            info@solaryatravel.it
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
