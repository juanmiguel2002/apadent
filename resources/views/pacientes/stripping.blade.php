@extends('layouts.app')
@section('pageTitle', 'Stripping del paciente '. $paciente->name)
@section('header')
    <div class="w-4/5 flex justify-start items-center">
        <a href="{{ route('pacientes-show', $paciente->id) }}" clas="mr-4">
            <img class="w-6 mr-2" src="{{ asset('storage/recursos/icons/volver_naranja.png') }}" alt="Volver">
        </a>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ 'Stripping del paciente ' }}
            <a href="{{ route('pacientes-show', $paciente->id) }}" class="text-azul hover:underline">
                {{ $paciente->name }} - {{ $paciente->num_paciente }}
            </a>
        </h2>
    </div>
@endsection

@section('content')
    <livewire:pacientes.imagenes-etapa :etapa="null" :paciente="$paciente" :tipo="$tipo" />
@endsection
