@extends('layouts.admin')

@section('title', 'Nuovo catamarano')

@section('content')
    {{-- Page header --}}
    <div class="dash-page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.catamarans.index') }}" class="dash-icon-btn" title="Torna alla flotta">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1>Nuovo catamarano</h1>
                <p>Aggiungi un nuovo catamarano alla flotta. Le immagini si potranno caricare dopo il salvataggio.</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.catamarans.store') }}" method="POST" id="catamaran-form">
        @csrf

        @include('admin.catamarans._form', ['catamaran' => null])

        <div class="dash-card mb-4">
            <div class="dash-card-body d-flex justify-content-end gap-2">
                <a href="{{ route('admin.catamarans.index') }}" class="btn btn-light rounded-pill px-4 border fw-semibold">
                    Annulla
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                    <i class="bi bi-plus-lg me-2"></i>Crea catamarano
                </button>
            </div>
        </div>
    </form>
@endsection
