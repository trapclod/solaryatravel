@extends('layouts.app')

@section('title', 'Cookie Policy - Solarya Travel')

@section('content')
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="mx-auto" style="max-width:780px">
                <h1 class="display-5 fw-bold text-navy mb-4 font-serif">Cookie Policy</h1>
                
                <div class="text-secondary">
                    <p class="lead text-secondary">
                        Questa Cookie Policy spiega cosa sono i cookie, come li utilizziamo e quali 
                        sono le opzioni disponibili per gestirli.
                    </p>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">1. Cosa sono i Cookie</h2>
                    <p>
                        I cookie sono piccoli file di testo che vengono memorizzati sul tuo dispositivo 
                        quando visiti un sito web. Sono ampiamente utilizzati per far funzionare i siti 
                        web in modo più efficiente e fornire informazioni ai proprietari del sito.
                    </p>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">2. Tipi di Cookie Utilizzati</h2>
                    
                    <h3 class="h5 fw-semibold text-navy mt-4 mb-2 font-serif">Cookie Tecnici (Necessari)</h3>
                    <p>
                        Questi cookie sono essenziali per il funzionamento del sito e non possono essere 
                        disattivati. Includono:
                    </p>
                    <ul class="ps-4">
                        <li>Cookie di sessione per la gestione dell'autenticazione</li>
                        <li>Cookie per la gestione del carrello/prenotazioni</li>
                        <li>Cookie per le preferenze di privacy</li>
                    </ul>

                    <h3 class="h5 fw-semibold text-navy mt-4 mb-2 font-serif">Cookie Analitici</h3>
                    <p>
                        Questi cookie ci aiutano a capire come i visitatori interagiscono con il sito, 
                        raccogliendo informazioni in forma anonima. Utilizziamo:
                    </p>
                    <ul class="ps-4">
                        <li>Google Analytics - per statistiche di traffico</li>
                    </ul>

                    <h3 class="h5 fw-semibold text-navy mt-4 mb-2 font-serif">Cookie di Marketing</h3>
                    <p>
                        Questi cookie vengono utilizzati per tracciare i visitatori sui siti web 
                        e mostrare annunci più rilevanti:
                    </p>
                    <ul class="ps-4">
                        <li>Facebook Pixel - per campagne pubblicitarie mirate</li>
                        <li>Google Ads - per remarketing</li>
                    </ul>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">3. Gestione dei Cookie</h2>
                    <p>
                        Puoi gestire le tue preferenze sui cookie in qualsiasi momento attraverso 
                        il banner dei cookie presente sul sito o modificando le impostazioni del browser.
                    </p>
                    <p>
                        La maggior parte dei browser accetta automaticamente i cookie, ma puoi 
                        modificare le impostazioni per rifiutarli. Tieni presente che disabilitare 
                        i cookie potrebbe influire sulla funzionalità del sito.
                    </p>

                    <h3 class="h5 fw-semibold text-navy mt-4 mb-2 font-serif">Come gestire i cookie nei browser più comuni:</h3>
                    <ul class="ps-4">
                        <li>
                            <strong>Chrome:</strong> 
                            Impostazioni → Privacy e sicurezza → Cookie e altri dati dei siti
                        </li>
                        <li>
                            <strong>Firefox:</strong> 
                            Opzioni → Privacy e sicurezza → Cookie e dati dei siti web
                        </li>
                        <li>
                            <strong>Safari:</strong> 
                            Preferenze → Privacy → Cookie e dati dei siti web
                        </li>
                        <li>
                            <strong>Edge:</strong> 
                            Impostazioni → Privacy, ricerca e servizi → Cookie
                        </li>
                    </ul>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">4. Cookie di Terze Parti</h2>
                    <p>
                        Alcuni cookie possono essere installati da servizi di terze parti che compaiono 
                        sulle nostre pagine. Non abbiamo controllo diretto su questi cookie. 
                        Per maggiori informazioni, consulta le rispettive privacy policy:
                    </p>
                    <ul class="ps-4">
                        <li>
                            <a href="https://policies.google.com/privacy" target="_blank" rel="noopener" class="text-primary">
                                Google Privacy Policy
                            </a>
                        </li>
                        <li>
                            <a href="https://www.facebook.com/policy.php" target="_blank" rel="noopener" class="text-primary">
                                Facebook Privacy Policy
                            </a>
                        </li>
                        <li>
                            <a href="https://stripe.com/privacy" target="_blank" rel="noopener" class="text-primary">
                                Stripe Privacy Policy
                            </a>
                        </li>
                    </ul>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">5. Aggiornamenti</h2>
                    <p>
                        Questa Cookie Policy può essere aggiornata periodicamente. Ti invitiamo a 
                        consultare questa pagina regolarmente per essere informato su eventuali modifiche.
                    </p>

                    <h2 class="h3 fw-bold text-navy mt-5 mb-3 font-serif">6. Contatti</h2>
                    <p>
                        Per qualsiasi domanda sulla nostra Cookie Policy, contattaci a: 
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
