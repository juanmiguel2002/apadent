@extends('layouts.app')
@section('pageTitle', $carpeta->nombre)
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ $carpeta->nombre }}
    </h2>
@endsection
@section('content')
    {{-- @livewire('carpeta.carpetaShow', $id) --}}
    <livewire:carpeta.carpeta-show :id="$id"/>
@endsection
