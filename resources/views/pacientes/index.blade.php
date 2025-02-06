@extends('layouts.app')
@section('pageTitle', 'Lista de Pacientes')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Lista de Pacientes') }}
    </h2>
@endsection

@section('content')
    @livewire('pacientes')
@endsection
