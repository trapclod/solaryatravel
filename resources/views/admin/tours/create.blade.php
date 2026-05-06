@extends('layouts.admin')

@section('title', 'Nuovo tour')

@section('content')
    <div class="dash-page-header">
        <div>
            <h1>Nuovo tour</h1>
            <p>Crea un nuovo pacchetto tour.</p>
        </div>
        <a href="{{ route('admin.tours.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Indietro</a>
    </div>

    <form method="POST" action="{{ route('admin.tours.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        @include('admin.tours._form')
    </form>
@endsection
