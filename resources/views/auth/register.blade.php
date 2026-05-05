@extends('layouts.public')

@section('title', 'Registrati')

@section('content')
    <section class="breadcrumb__area breadcrumb__bg" style="background-image: url('{{ asset('assets/template/img/breadcrumb/breadcrumb.jpg') }}');">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb__content text-center">
                        <h3 class="breadcrumb__title">Registrati</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Registrati</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="tg-login-area pt-130 pb-130">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-8 col-md-10">
                    <div class="tg-login-wrapper">
                        <div class="tg-login-top text-center mb-30">
                            <h2>Crea il tuo account</h2>
                            <p>Unisciti a Solarya Travel per un'esperienza esclusiva.</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="tg-login-form">
                            <div class="tg-tour-about-review-form">
                                <form method="POST" action="{{ route('register') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6 mb-25">
                                            <input class="input" type="text" name="first_name" placeholder="Nome" value="{{ old('first_name') }}" required autofocus>
                                        </div>
                                        <div class="col-md-6 mb-25">
                                            <input class="input" type="text" name="last_name" placeholder="Cognome" value="{{ old('last_name') }}" required>
                                        </div>
                                        <div class="col-lg-12 mb-25">
                                            <input class="input" type="email" name="email" placeholder="E-mail" value="{{ old('email') }}" required autocomplete="username">
                                        </div>
                                        <div class="col-lg-12 mb-25">
                                            <input class="input" type="tel" name="phone" placeholder="Telefono (opzionale)" value="{{ old('phone') }}">
                                        </div>
                                        <div class="col-lg-12 mb-25">
                                            <input class="input" type="password" name="password" placeholder="Password (almeno 8 caratteri)" required autocomplete="new-password">
                                        </div>
                                        <div class="col-lg-12 mb-25">
                                            <input class="input" type="password" name="password_confirmation" placeholder="Conferma Password" required autocomplete="new-password">
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="review-checkbox d-flex align-items-center mb-15">
                                                <input class="tg-checkbox" type="checkbox" name="accept_terms" value="1" id="accept_terms" {{ old('accept_terms') ? 'checked' : '' }}>
                                                <label for="accept_terms" class="tg-label">
                                                    Accetto i <a href="{{ route('terms') }}" target="_blank">Termini e Condizioni</a> *
                                                </label>
                                            </div>
                                            <div class="review-checkbox d-flex align-items-center mb-25">
                                                <input class="tg-checkbox" type="checkbox" name="accept_privacy" value="1" id="accept_privacy" {{ old('accept_privacy') ? 'checked' : '' }}>
                                                <label for="accept_privacy" class="tg-label">
                                                    Acconsento alla <a href="{{ route('privacy') }}" target="_blank">Privacy Policy</a> *
                                                </label>
                                            </div>
                                            <div class="d-flex justify-content-end mb-25">
                                                <div class="tg-login-navigate">
                                                    <a href="{{ route('login') }}">Hai già un account? Accedi</a>
                                                </div>
                                            </div>
                                            <button type="submit" class="tg-btn w-100">Crea Account</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
