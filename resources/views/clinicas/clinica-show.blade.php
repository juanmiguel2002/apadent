{{-- View Clinica id --}}
@extends('layouts.app')
@section('pageTitle', 'Clínica '.$clinica->name)
@section('header')
    <h2 class="font-semibold text-3xl text-gray-800">
        {{ 'Clínica' }} <span class="text-azul"> {{$clinica->name}}</span>
    </h2>
@endsection
@section('content')
    @livewire('clinica-show', [$clinica, $users])
@endsection
