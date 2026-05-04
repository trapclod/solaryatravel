@extends('layouts.admin')

@section('title', 'Modifica '.$discount->code)

@section('content')
    <div class="dash-page-header">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.discounts.index') }}" class="dash-icon-btn" title="Torna ai codici sconto">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h1 class="mb-0">Modifica <span class="font-monospace text-primary">{{ $discount->code }}</span></h1>
                <p class="mt-1 mb-0">Aggiorna le condizioni del codice promozionale.</p>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.discounts.show', $discount) }}" class="btn btn-light border rounded-pill px-3 fw-semibold">
                <i class="bi bi-eye me-2"></i>Dettagli
            </a>
        </div>
    </div>

    <form action="{{ route('admin.discounts.update', $discount) }}" method="POST">
        @csrf
        @method('PUT')
        @include('admin.discounts._form')

        <div class="dash-card mt-3">
            <div class="dash-card-body d-flex justify-content-between gap-2 flex-wrap">
                <button type="button" class="btn btn-outline-danger rounded-pill px-3 fw-semibold"
                        data-bs-toggle="modal" data-bs-target="#deleteDiscountModal">
                    <i class="bi bi-trash me-2"></i>Elimina codice
                </button>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-light border rounded-pill px-4 fw-semibold">
                        Annulla
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold">
                        <i class="bi bi-check2 me-2"></i>Salva modifiche
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- Delete modal --}}
    <div class="modal fade" id="deleteDiscountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:1rem">
                <div class="modal-body text-center p-4">
                    <div class="mx-auto mb-3 rounded-circle bg-danger-subtle text-danger d-inline-flex align-items-center justify-content-center"
                         style="width:72px; height:72px">
                        <i class="bi bi-exclamation-triangle fs-2"></i>
                    </div>
                    <h4 class="fw-bold mb-2">Eliminare il codice {{ $discount->code }}?</h4>
                    <p class="text-muted mb-0">L'operazione è irreversibile. Se il codice è stato usato, l'eliminazione potrebbe non essere consentita.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4 border" data-bs-dismiss="modal">Annulla</button>
                    <form action="{{ route('admin.discounts.destroy', $discount) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-semibold">
                            <i class="bi bi-trash me-2"></i>Elimina definitivamente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
