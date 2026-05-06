@extends('layouts.admin')

@section('title', 'Modifica tour: ' . $tour->name)

@section('content')
    <div class="dash-page-header">
        <div>
            <h1>{{ $tour->name }}</h1>
            <p>Modifica i dettagli del tour.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.tours.departures.index', $tour) }}" class="btn btn-outline-primary">
                <i class="bi bi-calendar-event me-1"></i>Gestisci partenze
            </a>
            <a href="{{ route('admin.tours.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Indietro</a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.tours.update', $tour) }}" enctype="multipart/form-data" novalidate>
        @csrf @method('PUT')
        @include('admin.tours._form')
    </form>
@endsection
