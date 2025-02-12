@extends('layouts.app')
@section('pageTitle', 'Mi unidad')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Mi unidad') }}
    </h2>
@endsection
@section('content')
    @livewire('carpeta.carpetaComponent')
@endsection
