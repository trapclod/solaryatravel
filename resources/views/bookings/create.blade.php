@extends('layouts.public')

@section('title', 'Prenota — ' . $tour->name)

@section('content')
    <livewire:public.booking-form :tour="$tour" :departure="$departure" />
@endsection
