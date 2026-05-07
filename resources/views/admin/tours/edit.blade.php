@extends('layouts.admin')

@section('title', 'Modifica tour: ' . $tour->name)

@section('content')
    <div class="dash-page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.tours.index') }}" class="dash-icon-btn" title="Torna ai tour">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1>Modifica <span class="text-primary">{{ $tour->name }}</span></h1>
                <p>Aggiorna dettagli, fasce di prezzo, immagini e catamarani assegnati al tour.</p>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
        </div>
    </div>

    <form method="POST" action="{{ route('admin.tours.update', $tour) }}" enctype="multipart/form-data" novalidate>
        @csrf @method('PUT')
        @include('admin.tours._form')
    </form>
@endsection
