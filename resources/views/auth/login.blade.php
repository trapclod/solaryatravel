@extends('layouts.public')

@section('title', 'Accedi')

@section('content')
    {{-- Breadcrumb --}}
    <section class="breadcrumb__area breadcrumb__bg" style="background-image: url('{{ asset('assets/template/img/breadcrumb/breadcrumb.jpg') }}');">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb__content text-center">
                        <h3 class="breadcrumb__title">Accedi</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Accedi</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Login --}}
    <div class="tg-login-area pt-130 pb-130">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-8 col-md-10">
                    <div class="tg-login-wrapper">
                        <div class="tg-login-top text-center mb-30">
                            <h2>Accedi al tuo account</h2>
                            <p>Inserisci le tue credenziali per accedere.</p>
                        </div>

                        @if (session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif

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
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12 mb-25">
                                            <input class="input" type="email" name="email" placeholder="E-mail" value="{{ old('email') }}" required autofocus autocomplete="username">
                                        </div>
                                        <div class="col-lg-12 mb-25">
                                            <input class="input" type="password" name="password" placeholder="Password" required autocomplete="current-password">
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="review-checkbox d-flex align-items-center mb-25">
                                                    <input class="tg-checkbox" type="checkbox" name="remember" id="remember">
                                                    <label for="remember" class="tg-label">Ricordami</label>
                                                </div>
                                                <div class="tg-login-navigate mb-25">
                                                    <a href="{{ route('password.request') }}">Password dimenticata?</a>
                                                </div>
                                            </div>
                                            <button type="submit" class="tg-btn w-100">Accedi</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            Non hai un account? <a href="{{ route('register') }}">Registrati</a>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="small mb-2">Puoi prenotare anche senza registrazione</p>
                            <a href="{{ route('booking.start') }}">Prenota come ospite &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
