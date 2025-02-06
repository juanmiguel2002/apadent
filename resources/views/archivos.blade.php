@extends('layouts.app')
@section('pageTitle', 'Gestor de Archivos')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Gestor de Archivos') }}
    </h2>
@endsection
@section('content')
    @livewire('archivos.dashboard')
@endsection
