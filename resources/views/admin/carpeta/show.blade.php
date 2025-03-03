@extends('layouts.app')
@section('pageTitle', $carpeta->nombre)
@section('header')
    {{ Breadcrumbs::render('admin.carpeta.view', $carpeta) }}
@endsection
@section('content')
    <livewire:carpeta.carpeta-show :id="$id"/>
@endsection
