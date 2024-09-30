<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-gray-800">
            {{ 'Clínica' }} <span class="text-azul"> {{$clinica->name}}</span>
        </h2>
    </x-slot>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @livewire('clinica-show', [$clinica, $users])
    </div>
</x-app-layout>
