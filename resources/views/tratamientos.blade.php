@extends('layouts.app')
@section('pageTitle', 'Lista de Tratamientos')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Lista de Tratamientos') }}
    </h2>
@endsection
@section('content')
    @livewire('tratamientos')
@endsection
