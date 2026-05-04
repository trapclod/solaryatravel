@extends('layouts.admin')

@section('title', 'Nuovo codice sconto')

@section('content')
    <div class="dash-page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.discounts.index') }}" class="dash-icon-btn" title="Torna ai codici sconto">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="mb-0">Nuovo codice sconto</h1>
                <p class="mt-1 mb-0">Crea un codice promozionale e definiscine condizioni e validità.</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.discounts.store') }}" method="POST">
        @csrf
        @include('admin.discounts._form')

        <div class="dash-card mt-3">
            <div class="dash-card-body d-flex justify-content-end gap-2 flex-wrap">
                <a href="{{ route('admin.discounts.index') }}" class="btn btn-light border rounded-pill px-4 fw-semibold">
                    Annulla
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                    <i class="bi bi-plus-lg me-2"></i>Crea codice
                </button>
            </div>
        </div>
    </form>
@endsection
