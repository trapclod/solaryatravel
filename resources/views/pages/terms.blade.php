@extends('layouts.app')

@section('title', 'Termini e Condizioni - Solarya Travel')

@section('content')
    <section class="py-16 lg:py-24 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="max-w-3xl mx-auto">
                <h1 class="text-4xl font-bold text-navy-900 mb-8">Termini e Condizioni</h1>
                
                <div class="prose prose-lg max-w-none text-gray-600">
                    <p class="lead">
                        I presenti Termini e Condizioni regolano l'utilizzo del sito web e dei servizi 
                        offerti da Solarya Travel S.r.l.
                    </p>

                    <h2 class="text-2xl font-bold text-navy-900 mt-8 mb-4">1. Accettazione dei Termini</h2>
                    <p>
                        Utilizzando il sito e i servizi di Solarya Travel, l'utente accetta integralmente 
                        i presenti Termini e Condizioni. Se non si accettano tali condizioni, si prega 
                        di non utilizzare il sito.
                    </p>

                    <h2 class="text-2xl font-bold text-navy-900 mt-8 mb-4">2. Prenotazioni</h2>
                    <p>
                        La prenotazione di un'escursione è vincolante una volta completato il pagamento. 
                        Al momento della prenotazione, l'utente riceverà una conferma via email con tutti 
                        i dettagli dell'esperienza.
                    </p>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>È necessario presentarsi al punto di imbarco almeno 30 minuti prima della partenza</li>
                        <li>È obbligatorio presentare la conferma di prenotazione (digitale o stampata)</li>
                        <li>I documenti di identità devono essere validi</li>
                    </ul>

                    <h2 class="text-2xl font-bold text-navy-900 mt-8 mb-4">3. Prezzi e Pagamenti</h2>
                    <p>
                        I prezzi indicati sul sito sono comprensivi di IVA e di tutti i servizi descritti. 
                        I pagamenti sono elaborati tramite gateway sicuri e certificati.
                    </p>

                    <h2 class="text-2xl font-bold text-navy-900 mt-8 mb-4">4. Cancellazioni e Rimborsi</h2>
                    <ul class="list-disc pl-6 space-y-2">
                        <li><strong>Cancellazione fino a 7 giorni prima:</strong> Rimborso totale</li>
                        <li><strong>Cancellazione da 7 a 3 giorni prima:</strong> Rimborso del 50%</li>
                        <li><strong>Cancellazione meno di 3 giorni prima:</strong> Nessun rimborso</li>
                        <li><strong>Mancata presentazione:</strong> Nessun rimborso</li>
                    </ul>
                    <p class="mt-4">
                        In caso di cancellazione per maltempo da parte di Solarya Travel, 
                        sarà offerta una data alternativa o il rimborso completo.
                    </p>

                    <h2 class="text-2xl font-bold text-navy-900 mt-8 mb-4">5. Responsabilità</h2>
                    <p>
                        Solarya Travel adotta tutte le misure necessarie per garantire la sicurezza degli ospiti. 
                        L'utente è tenuto a seguire le istruzioni dell'equipaggio e a comportarsi in modo 
                        responsabile durante l'escursione.
                    </p>
                    <p>
                        Solarya Travel non è responsabile per:
                    </p>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Oggetti personali smarriti o danneggiati</li>
                        <li>Danni derivanti dal mancato rispetto delle istruzioni di sicurezza</li>
                        <li>Eventi di forza maggiore (condizioni meteo, emergenze sanitarie, ecc.)</li>
                    </ul>

                    <h2 class="text-2xl font-bold text-navy-900 mt-8 mb-4">6. Requisiti per la Partecipazione</h2>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>I minori devono essere accompagnati da un adulto responsabile</li>
                        <li>È consigliato saper nuotare per le attività in acqua</li>
                        <li>In caso di condizioni di salute particolari, informare preventivamente l'equipaggio</li>
                        <li>Non è consentito l'imbarco in stato di ebbrezza</li>
                    </ul>

                    <h2 class="text-2xl font-bold text-navy-900 mt-8 mb-4">7. Proprietà Intellettuale</h2>
                    <p>
                        Tutti i contenuti del sito (testi, immagini, loghi, grafiche) sono di proprietà 
                        di Solarya Travel e sono protetti dalle leggi sul diritto d'autore.
                    </p>

                    <h2 class="text-2xl font-bold text-navy-900 mt-8 mb-4">8. Modifiche ai Termini</h2>
                    <p>
                        Solarya Travel si riserva il diritto di modificare i presenti Termini e Condizioni 
                        in qualsiasi momento. Le modifiche entreranno in vigore dalla pubblicazione sul sito.
                    </p>

                    <h2 class="text-2xl font-bold text-navy-900 mt-8 mb-4">9. Legge Applicabile</h2>
                    <p>
                        I presenti Termini e Condizioni sono regolati dalla legge italiana. 
                        Per qualsiasi controversia sarà competente il Foro di Roma.
                    </p>

                    <h2 class="text-2xl font-bold text-navy-900 mt-8 mb-4">10. Contatti</h2>
                    <p>
                        Per qualsiasi domanda sui presenti Termini e Condizioni, contattaci a: 
                        <a href="mailto:info@solaryatravel.it" class="text-primary-600 hover:text-primary-700">
                            info@solaryatravel.it
                        </a>
                    </p>

                    <p class="text-sm text-gray-500 mt-8">
                        Ultimo aggiornamento: {{ date('d/m/Y') }}
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection
