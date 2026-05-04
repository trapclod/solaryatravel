@extends('layouts.admin')

@section('title', 'Modifica ' . $addon->name)

@section('content')
    <div class="dash-page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.addons.index') }}" class="dash-icon-btn" title="Torna agli extra">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1>Modifica <span class="text-primary">{{ $addon->name }}</span></h1>
                <p>Aggiorna prezzo, descrizione, immagine e disponibilità dell'extra.</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.addons.show', $addon) }}" class="btn btn-light rounded-pill px-3 fw-semibold border">
                <i class="bi bi-eye me-2"></i>Dettagli
            </a>
        </div>
    </div>

    <form action="{{ route('admin.addons.update', $addon) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('admin.addons._form', ['addon' => $addon])

        <div class="dash-card mb-4">
            <div class="dash-card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <button type="button" class="btn btn-outline-danger rounded-pill px-3 fw-semibold"
                        data-bs-toggle="modal" data-bs-target="#deleteAddonModal">
                    <i class="bi bi-trash me-2"></i>Elimina extra
                </button>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.addons.show', $addon) }}" class="btn btn-light rounded-pill px-4 border fw-semibold">Annulla</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                        <i class="bi bi-check-lg me-2"></i>Salva modifiche
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- Delete confirm modal --}}
    <div class="modal fade" id="deleteAddonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:1rem">
                <div class="modal-body text-center p-4">
                    <div class="mx-auto mb-3 rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center" style="width:72px;height:72px">
                        <i class="bi bi-exclamation-triangle fs-2"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Eliminare {{ $addon->name }}?</h4>
                    <p class="text-muted">L'azione è irreversibile. Se ci sono prenotazioni associate l'eliminazione fallirà.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal">Annulla</button>
                    <form action="{{ route('admin.addons.destroy', $addon) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">
                            <i class="bi bi-trash me-2"></i>Elimina
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
