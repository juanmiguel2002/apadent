
@extends('layouts.app')
@section('pageTitle', 'Perfil de '. $paciente->name)
@section('header')
    <h2 class="font-semibold text-3xl text-gray-800">
        {{ 'Perfil de ' }} <span class="text-azul"> {{$paciente->name}} {{$paciente->apellidos}} - {{ $paciente->num_paciente }}</span>
    </h2>
@endsection

@section('content')
    <livewire:pacientes.paciente-show :id="$id" />
@endsection
