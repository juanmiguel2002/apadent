<div>
    <!-- botón Tratamiento y Documentación-->
    <div class="flex items-center justify-end mb-6 md:space-x-3">
        @if (!$tratId)
            <button  wire:click="showTratamientosModal" class="flex items-center px-4 py-2 bg-azul text-white rounded-lg shadow-md hover:bg-teal-500 focus:outline-none focus:ring-2 focus:ring-green-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                <span>Añadir Tratamiento</span>
            </button>
        @endif

        <button wire:click="showDocumentacionModal" class="flex items-center px-4 py-2 bg-azul text-white rounded-lg shadow-md hover:bg-teal-500 focus:outline-none focus:ring-2 focus:ring-green-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            <span>Documentación</span>
        </button>
    </div>

    @if ($tratId)
        <h2 class="px-2 mb-4 font-semibold text-lg text-gray-800">Tratamiento seleccionado: {{ $tratamiento->tratamiento->name }} - {{ $tratamiento->tratamiento->descripcion }}</h2>
    @else
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Tratamientos</h2>
            <select wire:model="tratamientoId" wire:change="loadEtapas" class="w-full p-3 border rounded-md">
                <option value="">Seleccione un tratamiento</option>
                @foreach($tratamiento as $trat)
                    <option value="{{ $trat->tratamiento->id }}">{{ $trat->tratamiento->name }} - {{ $trat->tratamiento->descripcion }}</option>
                @endforeach
            </select>
        </div>
    @endif

    @if($etapas)
        <x-tabla>
            <table class="min-w-full bg-gris">
                <thead>
                    <tr>
                        <th class="px-4 py-2 bg-azul">ID</th>
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
                            <td class="px-4 text-center">{{$etapa->id}}</td>
                            <td class="px-4 py-2 text-center">{{ $etapa->name }}</td>
                            <td class="px-4 py-2">
                                @foreach ($etapa->mensajes as $mensaje)
                                    <div class="text-left mb-2">
                                        <p class="text-azul font-light text-md rounded-md">{{ $mensaje->mensaje }}</p>
                                        <p class="text-xs font-normal p-2">{{ $mensaje->user->name }}</p>
                                        <div class="flex justify-between items-center">
                                            <p class="text-xs font-light text-gray-500">{{ $mensaje->created_at->format('d-m-Y H:i') }}</p>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="flex items-center mt-2">
                                    <input wire:model="mensajes.{{$etapa->id}}" name="mensaje" id="mensaje" placeholder="Escribe tu mensaje..." type="text" class="block w-full px-3 py-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-azul-light focus:border-transparent font-light text-xs">
                                    <button wire:click="enviarMensaje({{ $etapa->id }})" class="ml-2 px-3 py-1 bg-azul text-white rounded-md text-xs font-semibold hover:bg-azul-dark focus:outline-none focus:ring-2 focus:ring-azul-light focus:ring-opacity-50">
                                        Enviar
                                    </button>
                                </div>
                                <!-- Mostrar mensaje de error -->
                                @error('mensajes.' . $etapa->id)
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </td>
                            <td class="p-3 text-center flex justify-center items-center h-full mt-4">
                                @foreach (['En proceso' => 'bg-green-600', 'Pausado' => 'bg-blue-600', 'Finalizado' => 'bg-red-600', 'Set Up' => 'bg-yellow-600'] as $status => $color)
                                    @if ($etapa->status == $status)
                                        <div class="flex items-center space-x-2">
                                            <button wire:click="toggleMenu({{ $etapa->id }})"
                                                class="flex items-center justify-center px-6 text-white {{ $color }} font-medium rounded-xl {{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                @if ($etapa->status == 'Finalizado') disabled @endif>
                                                <span>{{ $status }}</span>
                                            </button>

                                            <!-- Icono de flecha solo si se puede mostrar el submenú -->
                                            @if ($etapa->etapa_status != 'Finalizado')
                                                <img class="ml-2 w-3 cursor-pointer" alt="Icono desplegable"
                                                    src="{{ asset('storage/recursos/icons/flecha_abajo.png') }}"
                                                    wire:click="toggleMenu({{ $etapa->id }})">
                                            @endif
                                        </div>

                                        <!-- Submenú con visibilidad específica para cada etapa -->
                                        @if ($etapa->status != 'Finalizado' && !empty($mostrarMenu[$etapa->id]))
                                            <div class="ml-8 mt-2 space-y-1">
                                                @foreach ($statuses as $optionStatus => $optionColor)
                                                    <div class="cursor-pointer text-white {{ $optionColor }} py-1 px-2 rounded-lg hover:bg-opacity-75"
                                                        wire:click="estado({{ $pacienteId }}, {{ $etapa->id }}, '{{ $optionStatus }}')">
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
                                    <button wire:click="abrirModalRevision({{ $etapa->id }})" class="{{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : '' }} mt-2 px-4 py-1 bg-indigo-600 text-white rounded-md text-xs font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:ring-opacity-50 transition duration-200">
                                        Asignar Fecha
                                    </button>
                                @endif
                            </td>
                            {{-- Archivos --}}
                            <td class="px-4 py-2">
                                <div class="flex justify-center items-center">
                                    @if ($this->tieneArchivos($etapa->id, true) == true)
                                        <a href="{{ route('archivo.descargar', ['filePath' => $archivo[0]->ruta]) }}" class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-800 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 16 18">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 1v11m0 0 4-4m-4 4L4 8m11 4v3a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-3"/>
                                            </svg>
                                            <span class="cursor-pointer font-light text-sm">Descargar</span>
                                        </a>
                                    @else
                                        <img class="w-4 mr-2 mt-2 {{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : '' }}" src="{{ asset('storage/recursos/icons/suma_azul.png') }}" alt="">
                                        <span wire:click="showModalArchivo()" class="mr-2 cursor-pointer font-light text-sm {{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : '' }}">Añadir</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Imágenes --}}
                            <td class="px-4 py-2">
                                <div class="flex justify-center items-center">
                                    @if ($this->tieneArchivos($etapa->id, false) == true)
                                        <!-- Mostrar botón 'Ver' si tiene archivos -->
                                        <img class="w-4 ml-4 mr-2" src="{{ asset('storage/recursos/icons/ojo_azul.png') }}">
                                        <span wire:click="verImg({{ $etapa->id }})" class="cursor-pointer font-light text-sm">Ver</span>
                                    @else
                                        <!-- Mostrar botón 'Añadir' si no tiene archivos -->
                                        <img class="w-4 mr-2 mt-2" src="{{ asset('storage/recursos/icons/suma_azul.png') }}">
                                        <span wire:click="showModalImg()" class="cursor-pointer font-light text-sm">Añadir</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 text-red-500 text-lg text-center cursor-pointer flex justify-center items-center w-full">
                <img src="{{ asset('storage/recursos/icons/etapa.png') }}" alt="etapas" class="w-4 mr-1 pt-3">
                <strong wire:click='nuevaEtapa'>Añadir etapa</strong>
            </div>
        </x-tabla>
        <div class="mb-5"></div>
    @endif

    {{-- Añadir Tratamiento --}}
    @if ($showTratamientoModal)
        <x-dialog-modal wire:model="showTratamientoModal" >
            <x-slot name="title">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Seleccionar un Tratamiento</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
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
                <button type="button" wire:click="closeModal" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                <button type="button" wire:click="saveTratamiento" class="bg-blue-500 text-white px-4 py-2 rounded">
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

    {{-- Documentación --}}
        @if ($documents)
        <x-dialog-modal wire:model="documents" >
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
                    <x-label for="selectedEtapa" value="Seleccionar Etapa" />
                    <select wire:model="selectedEtapa" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">Seleccione una etapa</option>
                        @foreach ($etapas as $etapa)
                            <option value="{{ $etapa->id }}">{{ $etapa->etapa->name }}</option>
                        @endforeach
                    </select>
                    <br>
                    <x-label for="selectedEtapa" value="Nueva Documentación" />
                    <input type="file" wire:model="documentacion" class="block w-full px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 mb-4">
                </div>
            </x-slot>

            <x-slot name="footer">
                <button type="button" wire:click="closeModal" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                <button type="button" wire:click="saveDocumentacion({{$tratamiento->id}})" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Guardar
                </button>
            </x-slot>
        </x-dialog-modal>
    @endif

    {{-- Añadir IMG Etapa --}}
    @if ($modalImg)
        <x-dialog-modal wire:model="modalImg" >
            <x-slot name="title">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Añadir Imágenes</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </x-slot>

            <x-slot name="content">
                <form wire:submit.prevent="saveImg">
                    <x-label for="imagenes" value="Añadir Imágenes" />
                    <input type="file" multiple accept="image/*" wire:model="imagenes" class="block w-full px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 mb-4">
                    <x-input-error for="imagenes.*" />
                </form>
            </x-slot>

            <x-slot name="footer">
                <button type="button" wire:click="closeModal" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                <button type="submit" wire:click="saveImg({{$etapa->id}})" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Guardar
                </button>
            </x-slot>
        </x-dialog-modal>
    @endif

    {{-- Añadir Archivos Etapa --}}
    @if ($modalArchivo)
        <x-dialog-modal wire:model="modalArchivo" >
            <x-slot name="title">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Añadir Archivos (.zip)</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </x-slot>

            <x-slot name="content">
                <form wire:submit.prevent='saveArchivos'>
                    <x-label for="archivos" value="Añadir Archivos" />
                    <input type="file" multiple wire:model="archivos" class="block w-full px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 mb-4">
                    <x-input-error for="archivos.*" />
                </form>
            </x-slot>

            <x-slot name="footer">
                <button type="button" wire:click="closeModal" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                <button type="button" wire:click="saveArchivos({{$etapa->id}})" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Guardar
                </button>
            </x-slot>
        </x-dialog-modal>
    @endif
</div>
