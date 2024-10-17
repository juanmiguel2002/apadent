<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800">
            {{ 'Perfil de ' }} <span class="text-azul"> {{$paciente->name}} {{$paciente->apellidos}} - {{ $paciente->num_paciente }}</span>
        </h2>
    </x-slot>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <livewire:paciente-show :id="$id" />
    </div>
</x-app-layout>
