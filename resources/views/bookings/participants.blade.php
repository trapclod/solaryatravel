@extends('layouts.public')

@section('title', 'Dati partecipanti - ' . $booking->booking_number)

@section('content')
<section class="py-5" style="background:#f8fafc;min-height:100vh">
    <div class="container">
        @livewire('public.participants-form', ['booking' => $booking])
    </div>
</section>
@endsection
