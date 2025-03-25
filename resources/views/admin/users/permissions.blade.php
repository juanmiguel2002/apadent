@extends('layouts.app')
@section('pageTitle', 'Lista de Permisos')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Listado de Permisos') }}
    </h2>
@endsection
@section('content')
    @livewire('Admin.PermissionsManager')
@endsection
