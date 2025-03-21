<div>
    <!-- botón Tratamiento y Documentación-->
    <div class="flex items-center justify-end mb-6 md:space-x-3">
        @if (!$tratId)
        <button wire:click="showTratModal"
            class="flex items-center px-4 py-2 bg-azul text-white rounded-lg shadow-md hover:bg-teal-500 focus:outline-none focus:ring-2 focus:ring-green-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                    clip-rule="evenodd" />
            </svg>
            <span>Añadir Tratamiento</span>
        </button>
        @endif

        <button wire:click="showDocumentacionModal"
            class="flex items-center px-4 py-2 bg-azul text-white rounded-lg shadow-md hover:bg-teal-500 focus:outline-none focus:ring-2 focus:ring-green-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                    clip-rule="evenodd" />
            </svg>
            <span>Documentación</span>
        </button>
    </div>

    @if ($tratId)
        <h2 class="px-2 mb-4 font-semibold text-lg text-gray-800">
            Tratamiento seleccionado: {{ $tratamientoPaciente->tratamiento->name }} - {{
            $tratamientoPaciente->tratamiento->descripcion }}
        </h2>
    @else
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Tratamientos</h2>
            <select wire:model="tratamientoId" wire:change.defer='loadEtapas($event.target.value)'
                class="w-full p-3 border rounded-md">
                <option value="">Seleccione un tratamiento</option>
                @foreach($tratamientoPaciente as $trat)
                <option value="{{ $trat->tratamiento->id }}">{{ $trat->tratamiento->name }} - {{
                    $trat->tratamiento->descripcion }}</option>
                @endforeach
            </select>
        </div>
    @endif

    @if ($etapas && count($etapas) > 0)
        <div class="space-y-4">
            <div class="bg-white shadow-md rounded-md space-y-4">
                <span
                    class="w-full flex justify-between items-center px-4 py-3 bg-azul text-white font-semibold rounded-t-md focus:outline-none">Fase
                    1</span>
                <div class="px-2 py-2">
                    <table class="table-fixed min-w-full bg-gray-50">
                        <thead>
                            <tr class='bg-azul'>
                                <th class="px-2 py-2" style="width: 10%">Etapa</th>
                                <th class="w-1/2 px-4 py-2">Mensaje</th>
                                <th class="w-1/6 px-4 py-2">Estado</th>
                                <th class="w-1/6 px-3 py-2">Revisión</th>
                                <th class="w-1/5 px-4 py-2">Archivos</th>
                                <th class="px-2 py-2">Rayos</th>
                                <th class="px-2 py-2">CBCT</th>
                                <th class="px-2 py-2">Fotografíes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($etapas as $etapa)
                            <tr class="hover:bg-gray-200 transition duration-300">
                                <td class="px-2 py-2 text-center">{{ $etapa->name }}</td>
                                <td class="px-4 py-2">
                                    @foreach ($etapa->mensajes as $mensaje)
                                    <div class="text-left mb-2">
                                        <p class="text-azul font-light text-md rounded-md">{{ $mensaje->mensaje }}</p>
                                        <p class="text-xs font-normal p-2">{{ $mensaje->user->name }}</p>
                                        <div class="flex justify-between items-center">
                                            <p class="text-xs font-light text-gray-500">{{
                                                $mensaje->created_at->format('d-m-Y H:i') }}</p>
                                        </div>
                                    </div>
                                    @endforeach

                                    <div class="flex items-center mt-2">
                                        <input wire:model="mensajes.{{$etapa->id}}" name="mensaje" id="mensaje"
                                            placeholder="Escribe tu mensaje..." type="text"
                                            class="block w-full px-3 py-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-azul-light focus:border-transparent font-light text-xs">
                                        <button wire:click="enviarMensaje({{ $etapa->id }})"
                                            class="ml-2 px-3 py-1 bg-azul text-white rounded-md text-xs font-semibold hover:bg-azul-dark focus:outline-none focus:ring-2 focus:ring-azul-light focus:ring-opacity-50">
                                            Enviar
                                        </button>
                                    </div>

                                    <!-- Mostrar mensaje de error -->
                                    @error('mensajes.' . $etapa->id)
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td class="relative px-4 py-2 text-center">
                                    @foreach ($statuses as $status => $color)
                                    @if ($etapa->status == $status)
                                    <div class="relative min-h-[35px] flex items-center justify-center">
                                        @if (auth()->user()->hasRole('admin') || ($status !== 'Finalizado' &&
                                        auth()->user()))
                                        <button wire:click="toggleMenu({{ $etapa->id }})"
                                            class="flex items-center justify-center px-6 text-white {{ $color }} font-medium rounded-lg">
                                            <span>{{ $status }}</span>
                                        </button>
                                        <img class="ml-2 w-3" src="{{ asset('storage/recursos/icons/flecha_abajo.png') }}"
                                            alt="flecha_abajo">

                                        @if (!empty($mostrarMenu[$etapa->id]))
                                        <div class="ml-4 mt-2 space-y-1">
                                            @foreach ($statuses as $optionStatus => $optionColor)
                                            <div class="cursor-pointer text-white {{ $optionColor }} py-1 px-2 rounded-lg hover:bg-opacity-75"
                                                wire:click="estado({{ $etapa->id }}, '{{ $optionStatus }}')">
                                                {{ $optionStatus }}
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif
                                        @else
                                        <span
                                            class="absolute inset-0 flex items-center justify-center px-4 text-white {{ $color }} font-normal rounded-lg">
                                            {{ $status }}
                                        </span>
                                        @endif
                                    </div>
                                    @endif
                                    @endforeach
                                </td>

                                <td class="px-2 py-2 text-center">
                                    @if($etapa->revision)
                                    <span class="text-sm font-semibold">{{
                                        \Carbon\Carbon::parse($etapa->revision)->format('d-m-Y') }}</span>
                                    @else
                                    <button wire:click="{{ $etapa->status == 'Finalizado' ? '' : "
                                        abrirModalRevision($etapa->id)"}}" class="{{ $etapa->status == 'Finalizado' ?
                                        'opacity-50 cursor-not-allowed' : '' }} mt-2 px-4 py-1 bg-indigo-600 text-white
                                        rounded-md text-xs font-semibold hover:bg-indigo-700 focus:outline-none focus:ring-2
                                        focus:ring-indigo-300 focus:ring-opacity-50 transition duration-200">
                                        Asignar Fecha
                                    </button>
                                    @endif
                                </td>

                                {{-- Archivos complementarios --}}
                                @if ($this->tieneArchivos($etapa->id, false, 'archivocomplementarios') == true)
                                <td class="px-2 py-2">
                                    <div class="flex justify-center items-center">
                                        <!-- Mostrar botón 'Ver' si tiene archivos -->
                                        <img class="w-4 ml-4 mr-2" src="{{ asset('storage/recursos/icons/ojo_azul.png') }}">
                                        <a href="{{ route('imagenes.ver', ['paciente' => $paciente->id, 'etapa' => $etapa->id, 'tipo' => 'archivocomplementarios']) }}"
                                            class="cursor-pointer font-light text-sm">Ver</a>
                                    </div>
                                </td>
                                @else
                                <td class="px-2 py-2">
                                    <div class="flex justify-center items-center">
                                        <p class="text-base text-center">Sin archivos</p>
                                    </div>
                                </td>
                                @endif

                                {{-- Rayos --}}
                                <td class="px-2 py-2">
                                    <div class="flex justify-center items-center">
                                        @if ($this->tieneArchivos($etapa->id, false, 'rayos') == true)
                                        <!-- Mostrar botón 'Ver' si tiene archivos -->
                                        <img class="w-4 ml-4 mr-2" src="{{ asset('storage/recursos/icons/ojo_azul.png') }}">
                                        <a href="{{ route('imagenes.ver', ['paciente' => $paciente->id, 'etapa' => $etapa->id, 'tipo' => 'rayos']) }}"
                                            class="cursor-pointer font-light text-sm">Ver</a>
                                        @else
                                        <!-- Mostrar botón 'Añadir' si no tiene archivos -->
                                        <img class="w-4 mr-2 mt-2 {{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : '' }}"
                                            src="{{ asset('storage/recursos/icons/suma_azul.png') }}">
                                        <span wire:click="{{ $etapa->status == 'Finalizado' ? '' : "
                                            showModalImg($etapa->id, 'rayos')"}}" class="cursor-pointer font-light text-sm
                                            {{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : ''
                                            }}">Añadir</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- CBCT --}}
                                <td class="px-2 py-2">
                                    <div class="flex justify-center items-center">
                                        @if ($this->tieneArchivos($etapa->id, true, 'cbct') == true)
                                        <!-- Opción para descargar archivo -->
                                        <a href="{{ route('archivo.descargar', ['filePath' => $archivo[0]->ruta]) }}"
                                            class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-800 mr-2" aria-hidden="true">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M8 1v11m0 0 4-4m-4 4L4 8m11 4v3a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-3" />
                                            </svg>
                                            <span class="cursor-pointer font-light text-sm">Descargar</span>
                                        </a>
                                        @else
                                        <!-- Opción para añadir archivo -->
                                        <div class="flex items-center">
                                            <img class="w-4 mr-2 mt-2 {{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                src="{{ asset('storage/recursos/icons/suma_azul.png') }}"
                                                alt="Añadir archivo">
                                            <span
                                                class="mr-2 cursor-pointer font-light text-sm {{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : '' }}"><a
                                                    href="{{ url('/subir?pacienteId=' . $paciente->id . '&etapaId=' . $etapa->id) }}">Añadir</a>
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Imágenes --}}
                                <td class="px-2 py-2">
                                    <div class="flex justify-center items-center">
                                        @if ($this->tieneArchivos($etapa->id, false, 'imgetapa') == true)
                                        <!-- Mostrar botón 'Ver' si tiene archivos -->
                                        <img class="w-4 ml-4 mr-2" src="{{ asset('storage/recursos/icons/ojo_azul.png') }}">
                                        <a href="{{ route('imagenes.ver', ['paciente' => $paciente->id, 'etapa' => $etapa->id, 'tipo' => 'imgetapa']) }}"
                                            class="cursor-pointer font-light text-sm">Ver</a>
                                        @else
                                        <!-- Mostrar botón 'Añadir' si no tiene archivos -->
                                        <img class="w-4 mr-2 mt-2 {{ $etapa->status == 'Finalizado' ? 'opacity-50 cursor-not-allowed' : '' }}"
                                            src="{{ asset('storage/recursos/icons/suma_azul.png') }}">
                                        <span wire:click="{{ $etapa->status == 'Finalizado' ? '' : "
                                            showModalImg($etapa->id, 'imgetapa')" }}"
                                            class="cursor-pointer font-light text-sm {{ $etapa->status == 'Finalizado' ?
                                            'opacity-50 cursor-not-allowed' : '' }}">
                                            Añadir
                                        </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div
                        class="px-6 text-red-500 text-lg text-center cursor-pointer flex justify-center items-center w-full">
                        <img src="{{ asset('storage/recursos/icons/etapa.png') }}" alt="etapas" class="w-4 mr-1 pt-3">
                        <strong wire:click='nuevaEtapa({{$tratamientoId ? $tratamientoId : $tratId}})'>Añadir etapa</strong>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Añadir Tratamiento --}}
    @if ($showTratamientoModal)
        <x-dialog-modal wire:model="showTratamientoModal">
            <x-slot name="title">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Seleccionar un Nuevo tratamiento</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </x-slot>

            <x-slot name="content">
                <!-- Selección de tratamiento existente -->
                <form wire:submit.prevent="saveTratamiento">
                    <x-label for="selectedTratamiento" value="Seleccionar Tratamiento" />
                    <select wire:model="selectedNewTratamiento"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">Seleccione un tratamiento</option>
                        @foreach ($tratamientos as $tratamiento)
                            <option value="{{ $tratamiento->id }}">{{ $tratamiento->name }} - {{ $tratamiento->descripcion }}
                            </option>
                        @endforeach
                    </select>
                    <br>
                    @if (session()->has('error'))
                    <div class="bg-red-500 text-white p-3 rounded-md mb-4">
                        {{ session('error') }}
                    </div>
                    @endif
                </form>
            </x-slot>

            <x-slot name="footer">
                <button type="button" wire:click="closeModal"
                    class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                <button type="submit" wire:click="saveTratamiento" class="bg-blue-500 text-white px-4 py-2 rounded">
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </x-slot>

            <x-slot name="content">
                <div>
                    <x-label for="selectedTratamiento" value="Próxima revisión" />
                    <input type="date" wire:model="revision"
                        class="block w-full px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 mb-4">
                </div>
            </x-slot>

            <x-slot name="footer">
                <button type="button" wire:click="closeModal"
                    class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                <button type="button" wire:click="revisionEtapa" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Guardar
                </button>
            </x-slot>
        </x-dialog-modal>
    @endif

    {{-- Añadir IMG Etapa --}}
    @if ($modalImg)
        <x-dialog-modal wire:model="modalImg">
            <x-slot name="title">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Añadir {{$tipo =='rayos' ? 'Rayos' : 'Imágenes'}}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </x-slot>

            <x-slot name="content">
                @include('components.alert-message')
                <form wire:submit.prevent="saveImg" enctype="multipart/form-data">
                    <x-label for="img" value="Añadir {{$tipo =='rayos' ? 'Rayos' : 'Imágenes'}}" />
                    <input type="file" multiple wire:model="imagenes"
                        class="block w-full px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 mb-4">
                    <p class="text-sm text-gray-500 mt-1">Tamaño máximo permitido: {{ $maxFileSize }} MB</p>
                    @error('imagenes')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </form>
            </x-slot>

            <x-slot name="footer">
                <button type="button" wire:click="closeModal"
                    class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                <button type="submit" wire:click="saveImg" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Guardar
                </button>
            </x-slot>
        </x-dialog-modal>
    @endif

    {{-- Añadir CBCT Etapa --}}
    {{-- @if ($modalArchivo)
    <x-dialog-modal wire:model="modalArchivo">
        <x-slot name="title">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Añadir CBCT (.zip)</h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </x-slot>

        <x-slot name="content">
            <div id="upload-container" class="text-center">
                <button id="browseFile"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    Seleccionar Archivo
                </button>
            </div>

            <div id="progress-container" class="hidden mt-3 w-full bg-gray-200 rounded overflow-hidden">
                <div id="progress-bar" class="h-6 bg-blue-500 text-white text-center text-sm font-semibold"
                    style="width: 0%;">
                    0%
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <button type="button" wire:click="closeModal"
                class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
        </x-slot>
    </x-dialog-modal>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
                let browseButton = document.getElementById("browseFile");
                let progressBar = document.getElementById("progress-bar");
                let progressContainer = document.getElementById("progress-container");

                let resumable = new Resumable({
                    target: "/upload",
                    query: { _token: "{{ csrf_token() }}" },
                    fileType: ['zip'],
                    chunkSize: 10 * 1024 * 1024, // 10MB por chunk
                    simultaneousUploads: 3,
                    testChunks: false,
                    throttleProgressCallbacks: 1,
                    maxFileSize: 4 * 1024 * 1024 * 1024, // 4GB
                });

                resumable.assignBrowse(browseButton);

                resumable.on('fileAdded', function(file) {
                    progressContainer.classList.remove("hidden");
                    resumable.upload();
                });

                resumable.on('fileProgress', function(file) {
                    let percentage = Math.floor(file.progress() * 100);
                    progressBar.style.width = percentage + "%";
                    progressBar.innerText = percentage + "%";
                });

                resumable.on('fileSuccess', function(file, response) {
                    alert("Archivo subido con éxito: " + JSON.parse(response).path);
                });

                resumable.on('fileError', function(file, message) {
                    alert("Error al subir el archivo: " + message);
                });
            });
    </script>
    @endif --}}

    @if ($documents)
        <x-dialog-modal wire:model="documents">
            <x-slot name="title">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Archivos Complementarios</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </x-slot>

            <x-slot name="content">
                @include('components.alert-message')
                <form wire:submit.prevent='saveDocumentacion' enctype="multipart/form-data">
                    <div>
                        <!-- Si se pasó un tratId por URL, mostrarlo directamente -->
                        @if ($tratId)
                            <p class="py-2 text-base"><strong>Tratamiento: {{ $tratamientoPaciente->tratamiento->name }} - {{
                                $tratamientoPaciente->tratamiento->descripcion }}</strong></p>
                        @elseif ($tratamientoId)
                            <p class="py-2 text-base"><strong>Tratamiento: {{ $tratamiento->name }} -
                                {{$tratamiento->descripcion}}</strong></p>
                        @else
                            <!-- Si no hay tratId, permitir elegir un tratamiento -->
                            <x-label for="tratamiento" value="Selecciona un tratamiento" />
                            <select wire:model="selectedtratamientoId" wire:change.defer='loadEtapas($event.target.value)'
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                                <option value="">Seleccione un tratamiento</option>
                                @foreach ($tratamientoPaciente as $trat)
                                    <option value="{{ $trat->tratamiento->id }}">{{ $trat->tratamiento->name }} - {{
                                    $trat->tratamiento->descripcion}}</option>
                                @endforeach
                            </select>
                            <br>
                        @endif
                        <x-label for="" value="Selecciona una etapa" />
                        <select wire:model="selectedEtapa"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <option value="">Seleccione una etapa</option>
                            @foreach ($etapas as $etapa)
                            <option value="{{ $etapa->id }}">{{ $etapa->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error for="selectedEtapa" />

                        <br>

                        <x-label for="documentacion" value="Nueva documentación" />
                        <input type="file" multiple wire:model="documentacion"
                            class="block w-full px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-indigo-600 mb-4">
                        <p class="text-sm text-gray-500">Tamaño máximo permitido: {{ $maxFileSize }} MB</p>
                        <x-input-error for="documentacion.*" />

                        <x-label for="mensaje" value="Mensaje o Descripción" class="mt-1" />
                        <textarea wire:model="mensaje"
                            class="block w-full px-3 py-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 mb-4"
                            rows="3"></textarea>
                        <x-input-error for="mensaje.*" />
                    </div>
                </form>
            </x-slot>

            <x-slot name="footer">
                <button type="button" wire:click="closeModal"
                    class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                <button type="submit" wire:click="saveDocumentacion" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Guardar
                </button>
            </x-slot>
        </x-dialog-modal>
    @endif
</div>
