@extends('layouts.app')
@section('pageTitle', 'Lista de Clínicas')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Listado de Clínicas') }}
    </h2>
@endsection
@section('content')
    @livewire('admin.clinicascomponent')
@endsection
