@extends('layouts.admin')

@section('title', 'Nuovo tour')

@section('content')
    <div class="dash-page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.tours.index') }}" class="dash-icon-btn" title="Torna ai tour">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1>Nuovo tour</h1>
                <p>Crea un nuovo pacchetto tour: definisci dettagli, fasce di prezzo e catamarani assegnati.</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.tours.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        @include('admin.tours._form')
    </form>
@endsection
