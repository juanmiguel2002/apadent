<div>
    <!-- botón Tratamiento-->
    <div class="flex items-center justify-end mb-6 md:space-x-3 ">
        <button wire:click="showTratamientosModal"  class="flex items-center px-4 py-2 bg-azul text-white rounded-lg shadow-md hover:bg-teal-500 focus:outline-none focus:ring-2 focus:ring-green-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            <span>Añadir Tratamiento</span>
        </button>
        <button wire:click="show>DocumentacionModal" class="flex items-center px-4 py-2 bg-azul text-white rounded-lg shadow-md hover:bg-teal-500 focus:outline-none focus:ring-2 focus:ring-green-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            <span>Documentación</span>
        </button>
    </div>

    <div class="mb-4">
        <label for="tratamientoId" class="block text-sm font-medium text-gray-700">Seleccionar Tratamiento:</label>
        <select wire:model.live="selectedTratamiento" class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            <option value="">Seleccione Tratamiento</option>
            @foreach ($tratamiento as $tratamiento)
                <option value="{{ $tratamiento->tratamiento->id }}">{{ $tratamiento->tratamiento->name }} - {{ $tratamiento->tratamiento->descripcion }}</option>
            @endforeach
        </select>
    </div>

    @if($etapas)
        <x-tabla>
            <table class="min-w-full bg-gris">
                <thead>
                    <tr>
                        <th class="px-4 py-2 bg-azul">Nº de Fase</th>
                        <th class="px-4 py-2 bg-azul">Mensaje</th>
                        <th class="px-4 py-2 bg-azul">Estado</th>
                        <th class="px-4 py-2 bg-azul">Revisión</th>
                        <th class="px-4 py-2 bg-azul">Archivos</th>
                        <th class="px-4 py-2 bg-azul">Imágenes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($etapas as $etapa)
                        <tr>
                            <td class="px-4 py-2 text-center">{{ $etapa->etapa->name }}</td>
                            <td class="px-4 py-2">
                                @foreach ($etapa->mensajes as $mensaje)
                                    <div class="text-left mb-2">
                                        <p class="text-sm font-semibold">{{ $mensaje->user->name }}</p>
                                        <p class="text-azul font-light text-md p-2 rounded-md">{{ $mensaje->mensaje }}</p>
                                        <div class="flex justify-between items-center">
                                            <p class="text-xs font-light text-gray-500">{{ $mensaje->created_at->format('d-m-Y H:i') }}</p>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="flex items-center mt-2">
                                    <input wire:model="mensaje" name="mensaje" id="mensaje" placeholder="Escribe tu mensaje..." type="text" class="block w-full px-3 py-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-azul-light focus:border-transparent font-light text-xs">
                                    <button wire:click="enviarMensaje({{ $etapa->id }})" class="ml-2 px-3 py-1 bg-azul text-white rounded-md text-xs font-semibold hover:bg-azul-dark focus:outline-none focus:ring-2 focus:ring-azul-light focus:ring-opacity-50">
                                        Enviar
                                    </button>
                                </div>
                            </td>
                            <td class="p-3 text-center flex justify-center items-center h-full mt-4">
                                @foreach (['En proceso' => 'bg-green-600', 'Pausado' => 'bg-blue-600', 'Finalizado' => 'bg-red-600', 'Set Up' => 'bg-yellow-600'] as $status => $color)
                                    @if ($etapa->status == $status)
                                        <button wire:click="toggleMenu" class="flex items-center justify-center px-6 text-white {{ $color }} font-medium rounded-xl {{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : '' }}" @if ($etapa->status == 'Finalizado') disabled @endif>
                                            <span>{{ $status }}</span>
                                        </button>
                                        <img class="ml-2 w-3" src="{{ asset('storage/recursos/icons/flecha_abajo.png') }}" alt="">
                                        @if ($etapa->status != 'Finalizado' && $mostrar)
                                            <div class="ml-4">
                                                @foreach ($statuses as $optionStatus => $optionColor)
                                                    <div wire:click="estado({{ $pacienteId}}, {{ $tratamiento->trat_id }}, '{{ $optionStatus }}')" class="cursor-pointer text-white {{ $optionColor }} my-2 rounded-lg hover:bg-opacity-75 px-2">
                                                        {{ $optionStatus }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </td>
                            <td class="px-4 py-2 text-center">
                                @if($etapa->revision)
                                    <span class="text-sm font-semibold">{{ \Carbon\Carbon::parse($etapa->revision)->format('d-m-Y') }}</span>
                                @else
                                    {{-- <span class="text-sm text-gray-500">Sin Asignar</span>
                                    <br> --}}
                                    <button wire:click="abrirModalRevision({{ $etapa->id }})" class="mt-2 px-4 py-1 bg-indigo-600 text-white rounded-md text-xs font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:ring-opacity-50 transition duration-200">
                                        Asignar Fecha
                                    </button>
                                @endif

                            </td>

                            <td class="px-4 py-2">
                                <div class="flex justify-center items-center">
                                    <img class="w-4 mr-2 mt-2 {{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : '' }}" src="{{ asset('storage/recursos/icons/suma_azul.png') }}" alt="">
                                    <span wire:click="showModalArchivo()" class="cursor-pointer font-light text-sm {{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : '' }}">Añadir</span>
                                    <img class="w-4 ml-4 mr-2 {{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : '' }}" src="{{ asset('storage/recursos/icons/ojo_azul.png') }}" alt="">
                                    <span wire:click="showArchivo()" class="cursor-pointer font-light text-sm {{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : '' }}">Ver</span>
                                </div>
                            </td>
                            <td class="px-4 py-2">
                                <div class="flex justify-center items-center">
                                    <img class="w-4 mr-2 mt-2 {{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : '' }}" src="{{ asset('storage/recursos/icons/suma_azul.png') }}" alt="">
                                    <span wire:click="showModalImg()" class="cursor-pointer font-light text-sm {{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : '' }}">Añadir</span>
                                    <img class="w-4 ml-4 mr-2 {{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : '' }}" src="{{ asset('storage/recursos/icons/ojo_azul.png') }}" alt="">
                                    <span wire:click="showImg()" class="cursor-pointer font-light text-sm {{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : '' }}">Ver</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div  class="px-6 text-red-500 text-lg text-center cursor-pointer flex justify-center items-center w-full">
                <img src="{{ asset('storage/recursos/icons/etapa.png') }}" alt="etapas" class="w-4 mr-1 pt-3">
                <strong wire:click='nuevaEtapa'>Añadir etapa</strong>
            </div>
        </x-tabla>
    @endif

    {{-- Añadir Tratamiento --}}
    @if ($showTratamientoModal)
        <x-dialog-modal wire:model="showTratamientoModal" >
            <x-slot name="title">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Seleccionar un Tratamiento</h3>
                    <button wire:click="closeTratamientosModal" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </x-slot>

            <x-slot name="content">
                <!-- Selección de tratamiento existente -->
                <div>
                    <x-label for="selectedTratamiento" value="Seleccionar Tratamiento" />
                    <select wire:model="selectedNewTratamiento" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">Seleccione un tratamiento</option>
                        @foreach ($tratamientos as $tratamiento)
                            <option value="{{ $tratamiento->id }}">{{ $tratamiento->name }} - {{ $tratamiento->descripcion }}</option>
                        @endforeach
                    </select>
                    @if (session()->has('error'))
                        <div class="bg-red-500 text-white p-3 rounded-md mb-4">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </x-slot>

            <x-slot name="footer">
                <button type="button" wire:click="closeTratamientosModal" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                <button type="button" wire:click="saveTratamiento({{$tratamiento->id}})" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Guardar
                </button>
            </x-slot>
        </x-dialog-modal>
    @endif

    {{-- Revisión --}}
    @if ($modalOpen)
        <x-dialog-modal wire:model="modalOpen" maxWidth="sm">
            <x-slot name="title">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Selecciona una fecha</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </x-slot>

            <x-slot name="content">
                <div>
                    <x-label for="selectedTratamiento" value="Próxima revisión" />
                    <input type="date" wire:model="revision" class="block w-full px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 mb-4">
                </div>
            </x-slot>

            <x-slot name="footer">
                <button type="button" wire:click="closeModal" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                <button type="button" wire:click="revisionEtapa" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Guardar
                </button>
            </x-slot>
        </x-dialog-modal>

    @endif
</div>
