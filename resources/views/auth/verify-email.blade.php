@extends('layouts.public')

@section('title', 'Verifica email')

@section('content')
<section class="py-5" style="background:#f8fafc;min-height:60vh">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="bg-white rounded-4 p-4 p-md-5 shadow-sm text-center" style="border:1px solid #e2e8f0">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width:72px;height:72px;background:rgba(0,102,204,.1);color:#0066cc;font-size:1.8rem">
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                    <h1 class="h3 fw-bold mb-2" style="color:#0E1B33">Verifica la tua email</h1>
                    <p class="text-muted mb-4">
                        Ti abbiamo inviato un'email di benvenuto contenente un link per confermare il tuo indirizzo.
                        Clicca il pulsante nella mail per attivare completamente l'account.
                    </p>

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success small d-flex align-items-center gap-2 text-start">
                            <i class="fa-solid fa-circle-check"></i>
                            <div>Una nuova mail di verifica è stata inviata. Controlla la tua casella (anche spam).</div>
                        </div>
                    @endif

                    <div class="p-3 rounded-3 small text-start mb-4" style="background:#f1f5f9;color:#475569">
                        <strong class="d-block mb-1" style="color:#0E1B33"><i class="fa-solid fa-circle-info me-1"></i>Non hai ricevuto la mail?</strong>
                        Controlla nella cartella spam. Se non la trovi, clicca qui sotto per richiederne una nuova.
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold">
                                <i class="fa-solid fa-paper-plane me-2"></i>Reinvia mail di verifica
                            </button>
                        </form>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link text-muted small text-decoration-none">
                                Esci dall'account
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
