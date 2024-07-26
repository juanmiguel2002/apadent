<div>
    @can('doctor_user')

    @endcan
    <div class="flex items-center justify-between mb-6">
        <!-- Contenedor del título -->
        <div class="flex items-center text-naranja cursor-pointer" wire:click='atras'>
            <img class="w-6 mr-2" src="{{ asset('storage/recursos/icons/volver_naranja.png') }}" alt="Volver">
            <p class="text-lg font-semibold">Pacientes</p>
        </div>

        <!-- Contenedor de los botones -->
        <div class="flex space-x-4">
            <!-- Primer botón -->
            @can('doctor_user')
                <button wire:click="stripping" class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Añadir Stripping</span>
                </button>
            @endcan

            <!-- botón Tratamiento-->
            <button wire:click="showTratamientosModal" class="flex items-center px-4 py-2 bg-green-500 text-white rounded-lg shadow-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                <span>Añadir Tratamiento</span>
            </button>
        </div>
    </div>
    <div class="my-3"></div>
    <div class="mx-auto p-6 bg-white shadow-md rounded-lg">
        <div class="flex space-x-6">
            <!-- Información del Paciente -->
            <div class="w-2/3 space-y-4">
                <p class="text-lg font-semibold text-gray-800">Nombre: <span class="text-gray-600">{{ $paciente->name }}</span></p>
                <p class="text-lg font-semibold text-gray-800">Email: <span class="text-gray-600">{{ $paciente->email }}</span></p>
                <p class="text-lg font-semibold text-gray-800">Teléfono: <span class="text-gray-600">{{ $paciente->telefono }}</span></p>
                <p class="text-lg font-semibold text-gray-800">Próxima revisión: <span class="text-gray-600">{{ date('d/m/Y', strtotime($paciente->revision)) }}</span></p>

                <!-- Cambiar fecha revisión -->
                <div class="flex items-center space-x-4">
                    <input type="date" wire:model="fecha" class="block px-3 py-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
                    <button wire:click="revision" class="text-blue-600 hover:underline">Cambiar fecha revisión</button>
                </div>

                <!-- Tratamientos -->
                <div class="mt-6">
                    <h3 class="text-xl font-semibold text-gray-800">Tratamientos:</h3>
                    @foreach ($tratamientos as $tratamiento)
                        <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-md">
                            <p class="text-lg font-semibold text-gray-800">Tratamiento: <span class="text-gray-600">{{ $tratamiento->tratamiento->name }}</span></p>
                            <p class="text-lg font-semibold text-gray-800">Fecha inicio tratamiento: <span class="text-gray-600">{{ $tratamiento->created_at->format('d/m/Y') }}</span></p>
                        </div>
                    @endforeach
                </div>

                <!-- Observaciones -->
                <p class="text-lg font-semibold text-gray-800">Observaciones stripping: <span class="text-gray-600">{{ $paciente->odontograma_observaciones }}</span></p>
                <p class="text-lg font-semibold text-gray-800">Observaciones: <span class="text-gray-600">{{ $paciente->observacion }}</span></p>
                <p class="text-lg font-semibold text-gray-800">Comentarios cbct: <span class="text-gray-600">{{ $paciente->obser_cbct }}</span></p>

                <!-- Añadir observaciones odontograma -->
                @if (Auth::user()->id == 1)
                    <textarea wire:model="obs_odontograma" class="w-full mt-4 p-2 text-gray-600 border border-gray-300 rounded-md shadow-sm" rows="4" placeholder="Añadir observaciones..."></textarea>
                    <button wire:click="ob_odontograma" class="mt-2 text-blue-600 hover:underline">Añadir observaciones odontograma</button>
                @endif
            </div>

            <!-- Botones para añadir imágenes y archivos -->
            <div class="w-2/4 space-y-4 text-center">
                <!-- Botones para añadir imágenes y archivos -->
                <div class="flex justify-center space-x-4 mb-8">
                    <!-- Botón para añadir imágenes -->
                    <button wire:click="showImagenes" class="flex items-center justify-center px-4 py-2 bg-azul outline-none rounded-3xl text-white shadow-lg font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        <span>Añadir Imágenes</span>
                    </button>

                    <!-- Botón para añadir archivos -->
                    <button wire:click="showCbct" class="flex items-center justify-center px-4 py-2 bg-azul outline-none rounded-3xl text-white shadow-lg font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        <span>Añadir Archivos CBCT</span>
                    </button>
                </div>

                <!-- Tabla para imágenes -->
                <div class="mt-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Imágenes</h2>
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
                        <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="px-4 py-2 text-left text-gray-600">Vista Previa</th>
                                <th class="px-4 py-2 text-center text-gray-600">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($imagenes as $imagen)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2">
                                        <a target="_blank" href="{{ Storage::url($imagen->ruta) }}">
                                            <img src="{{ Storage::url($imagen->ruta) }}" alt="Imagen" class="w-16 h-16 object-cover">
                                        </a>
                                    </td>
                                    <td class="px-4 py-2 flex items-center space-x-2">
                                        <a href="{{ Storage::url($imagen->ruta) }}" target="_blank" class="text-blue-500 hover:underline text-center">Ver</a>
                                        <a download="{{ Storage::url($imagen->ruta) }}" class="text-blue-500 hover:underline text-center">Descargar</a>
                                        @if(Auth::user()->hasRole('doctor'))
                                            <button wire:click="deleteImage({{ $imagen->id }})" class="text-red-500 hover:underline">Eliminar</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Tabla para archivos -->
                <div class="mt-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Archivos</h2>
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
                        <thead>
                            <tr class="bg-gray-100 border-b">
                                <th class="px-4 py-2 text-left text-gray-600">Nombre</th>
                                <th class="px-4 py-2 text-left text-gray-600">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($archivos as $archivo)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-2">
                                        <a href="{{ Storage::url($archivo->ruta) }}" target="_blank" class="text-blue-500 hover:underline">
                                            {{ basename($archivo->ruta) }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2 flex items-center space-x-2">
                                        <a href="{{ Storage::url($archivo->ruta) }}" target="_blank" class="text-blue-500 hover:underline">Descargar</a>
                                        @if(Auth::user()->hasRole('doctor'))
                                            <button wire:click="deleteArchivo({{ $archivo->id }})" class="text-red-500 hover:underline">Eliminar</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if ($showModal)
        <x-dialog-modal wire:model="showModal" maxWidth="lg">
            <x-slot name="title">
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-medium text-gray-800">{{ $uploadType == 'imagenes' ? 'Añadir Imágenes' : 'Añadir Archivos' }}</h1>
                    <button wire:click="close" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </x-slot>
            <x-slot name="content">
                <x-label for="file" class="block text-md text-azul capitalize" value="{{ $uploadType == 'imagenes' ? 'Imágenes' : 'Archivos' }}" />
                <x-input wire:model="files" multiple required type="file" class="block w-full px-3 py-2 mt-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-300 focus:ring-opacity-40" />
                <x-input-error for="files.*" />
            </x-slot>
            <x-slot name="footer">
                <button type="button" wire:click="close" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                <button type="submit" wire:click="save" class="bg-azul text-white px-4 py-2 rounded">Añadir</button>
            </x-slot>
        </x-dialog-modal>
    @endif

    @if ($showTratamientoModal)
        <x-dialog-modal wire:model="showTratamientoModal">
            <x-slot name="title">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Seleccionar o Añadir Tratamiento</h3>
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
                    <select wire:model="selectedTratamiento" id="selectedTratamiento" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">Seleccione un tratamiento</option>
                        @foreach ($tratamientosAll as $tratamiento)
                            <option value="{{ $tratamiento->id }}">{{ $tratamiento->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Opción para crear un nuevo tratamiento -->
                <div class="mt-4">
                    <x-label for="newTratamiento" value="O Crear Nuevo Tratamiento" />
                    <x-input wire:model="newTratamiento" id="newTratamiento" type="text" class="mt-1 block w-full" placeholder="Nombre del nuevo tratamiento" />
                    <x-input-error for="newTratamiento" />
                    <button type="button" wire:click="createNewTratamiento" class="mt-2 bg-green-500 text-white px-4 py-2 rounded">
                        Crear Nuevo
                    </button>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button type="button" wire:click="closeTratamientosModal" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                <button type="button" wire:click="saveTratamiento" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Guardar
                </button>
            </x-slot>
        </x-dialog-modal>
    @endif
</div>
