@extends('layouts.app')
@section('pageTitle', 'Lista de Usuarios')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Listado de Usuarios') }}
    </h2>
@endsection
@section('content')
    @livewire('Admin.Users.Users-management')
@endsection
