@extends('layouts.public')

@section('title', 'Reimposta Password')

@section('content')
    <section class="breadcrumb__area breadcrumb__bg" style="background-image: url('{{ asset('assets/template/img/breadcrumb/breadcrumb.jpg') }}');">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb__content text-center">
                        <h3 class="breadcrumb__title">Reimposta Password</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Reimposta Password</li>
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
                            <h2>Reimposta la password</h2>
                            <p>Inserisci la tua nuova password.</p>
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
                                <form method="POST" action="{{ route('password.store') }}">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                                    <div class="row">
                                        <div class="col-lg-12 mb-25">
                                            <input class="input" type="email" name="email" placeholder="E-mail" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
                                        </div>
                                        <div class="col-lg-12 mb-25">
                                            <input class="input" type="password" name="password" placeholder="Nuova Password (almeno 8 caratteri)" required autocomplete="new-password">
                                        </div>
                                        <div class="col-lg-12 mb-25">
                                            <input class="input" type="password" name="password_confirmation" placeholder="Conferma Password" required autocomplete="new-password">
                                        </div>
                                        <div class="col-lg-12">
                                            <button type="submit" class="tg-btn w-100">Reimposta Password</button>
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
