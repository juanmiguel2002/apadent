@extends('layouts.app')
@section('pageTitle', $paciente->num_paciente." - ". $paciente->name. " - ". $etapa->name)
@section('header')
    <div class="w-4/5 flex justify-start items-center">
        <a href="javascript: history.go(-1)" clas="mr-4">
            <img class="w-6 mr-2" src="{{ asset('storage/recursos/icons/volver_naranja.png') }}" alt="Volver">
        </a>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">

            @php
                $titulo = '';
                if ($tipo == 'rayos') {
                    $titulo = 'Radiografías';
                } else if ($tipo == 'archivocomplementarios') {
                    $titulo = 'Archivos Complementarios';
                }else {
                    $titulo = 'Fotografías';
                }
            @endphp
                {{ $titulo. ' del paciente ' }}

            <a href="{{ route('pacientes-show', $paciente->id) }}" class="text-azul hover:underline">
                {{ $paciente->name }} - {{ $paciente->num_paciente }}
            </a>
        </h2>
    </div>
@endsection

@section('content')
    <livewire:pacientes.imagenes-etapa :etapa="$etapa" :paciente="$paciente" :tipo="$tipo" />
@endsection
