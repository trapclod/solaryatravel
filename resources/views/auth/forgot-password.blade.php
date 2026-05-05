@extends('layouts.public')

@section('title', 'Password Dimenticata')

@section('content')
    <section class="breadcrumb__area breadcrumb__bg" style="background-image: url('{{ asset('assets/template/img/breadcrumb/breadcrumb.jpg') }}');">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb__content text-center">
                        <h3 class="breadcrumb__title">Password Dimenticata</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Password Dimenticata</li>
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
                            <h2>Password dimenticata?</h2>
                            <p>Inserisci la tua email e ti invieremo un link per reimpostarla.</p>
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
                                <form method="POST" action="{{ route('password.email') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-12 mb-25">
                                            <input class="input" type="email" name="email" placeholder="E-mail" value="{{ old('email') }}" required autofocus>
                                        </div>
                                        <div class="col-lg-12">
                                            <button type="submit" class="tg-btn w-100">Invia Link di Reset</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="{{ route('login') }}">&larr; Torna al login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
