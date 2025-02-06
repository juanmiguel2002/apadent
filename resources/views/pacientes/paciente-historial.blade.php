@extends('layouts.app')
@section('pageTitle', 'Historial de '. $paciente->name)
@section('header')
    <h2 class="font-semibold text-3xl text-gray-800">
        {{ 'Historial de ' }}
        <a href="{{ route('pacientes-show', $paciente->id) }}" class="text-azul hover:underline">
            {{ $paciente->name }} - {{ $paciente->num_paciente }}
        </a>
    </h2>
@endsection

@section('content')
    <div class="w-4/5 flex justify-start items-center mb-5">
        <a href="javascript: history.go(-1)" class="flex items-center mr-4">
            <img class="w-6 mr-2" src="{{ asset('storage/recursos/icons/volver_naranja.png') }}" alt="Volver">
            <p class="text-lg font-semibold text-naranja">Volver atrás</p>
        </a>
        <h2 class="text-gris-texto text-2xl font-normal">Clínica: <span class="text-azul font-medium">{{ $paciente->clinicas->name }}</span></h2>
    </div>

    <livewire:pacientes.historial-paciente :paciente="$paciente" :tratamiento="$tratamiento" :tratId="$tratId"/>
@endsection
