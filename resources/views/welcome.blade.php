@extends('layouts.app')

@section('title', 'Solarya Travel')

@section('content')
    <section class="section bg-gradient-hero text-white text-center">
        <div class="container py-5">
            <h1 class="font-serif fw-bold display-3 mb-3">Solarya Travel</h1>
            <p class="lead mb-4 mx-auto" style="max-width:640px;">Escursioni di lusso in catamarano sulle coste più belle del Mediterraneo</p>
            <a href="{{ route('home') }}" class="btn btn-gold btn-lg rounded-pill px-4">
                Scopri di più <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </section>
@endsection
