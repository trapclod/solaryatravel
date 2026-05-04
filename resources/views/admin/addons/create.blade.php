@extends('layouts.admin')

@section('title', 'Nuovo extra')

@section('content')
    <div class="dash-page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.addons.index') }}" class="dash-icon-btn" title="Torna agli extra">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1>Nuovo extra</h1>
                <p>Aggiungi un nuovo servizio o attività che i clienti potranno selezionare.</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.addons.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @include('admin.addons._form', ['addon' => null])

        <div class="dash-card mb-4">
            <div class="dash-card-body d-flex justify-content-end gap-2">
                <a href="{{ route('admin.addons.index') }}" class="btn btn-light rounded-pill px-4 border fw-semibold">Annulla</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                    <i class="bi bi-plus-lg me-2"></i>Crea extra
                </button>
            </div>
        </div>
    </form>
@endsection
