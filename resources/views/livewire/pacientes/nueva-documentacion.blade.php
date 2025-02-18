<div>
    {{-- Documentación --}}
    @if ($documents)
        <x-dialog-modal wire:model="documents">
            <x-slot name="title">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Selecciona una Etapa</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </x-slot>

            <x-slot name="content">
                <div>
                    <!-- Si se pasó un tratId por URL, mostrarlo directamente -->
                    @if ($tratamientoId)
                        <p class="py-2"><strong>Tratamiento:</strong> {{ $tratamiento->name }} - {{ $tratamiento->descripcion }}</p>
                    @else
                        <!-- Si no hay tratId, permitir elegir un tratamiento -->
                        <x-label for="tratamientoId" value="Seleccionar Tratamiento" />
                        <select wire:model="tratamientoId" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                            <option value="">Seleccione un tratamiento</option>
                            @foreach ($tratamientos as $trat)
                                <option value="{{ $trat->tratamiento->id }}">{{ $trat->tratamiento->name }} - {{ $trat->tratamiento->descripcion}}</option>
                            @endforeach
                        </select>
                        <br>
                    @endif
                    <x-label for="selectedEtapa" value="Seleccionar Etapa" />
                    <select wire:model="selectedEtapa" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">Seleccione una etapa</option>
                        @foreach ($etapas as $etapa)
                            <option value="{{ $etapa->id }}">{{ $etapa->name }}</option>
                        @endforeach
                    </select>

                    <br>

                    <x-label for="documentacion" value="Nueva Documentación" />
                    <input type="file" wire:model="documentacion" class="block w-full px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 mb-4">

                    <x-label for="mensaje" value="Mensaje o Descripción" />
                    <textarea wire:model="mensaje" class="block w-full px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 mb-4" rows="3"></textarea>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button type="button" wire:click="closeModal" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                <button type="button" wire:click="saveDocumentacion()" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Guardar
                </button>
            </x-slot>
        </x-dialog-modal>
    @endif
</div>
