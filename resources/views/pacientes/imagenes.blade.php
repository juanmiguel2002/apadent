<x-app-layout>
    <x-slot name="header">
        <div class="w-4/5 flex justify-start items-center">
            <a href="javascript: history.go(-1)" class="flex items-center mr-4">
                <img class="w-6 mr-2" src="{{ asset('storage/recursos/icons/volver_naranja.png') }}" alt="Volver">
                {{-- <p class="text-lg font-semibold text-naranja">Atrás</p> --}}
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Imágenes del paciente ') . $paciente->name . " " . $paciente->apellidos }}
            </h2>
        </div>
    </x-slot>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <livewire:imagenes-etapa :etapa="$etapa" :paciente="$paciente"/>
    </div>
</x-app-layout>
