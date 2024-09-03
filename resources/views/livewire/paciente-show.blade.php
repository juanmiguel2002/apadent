<div>
    {{-- <div class="flex items-center justify-between mb-6">
        <!-- Contenedor del título -->
        <a href="javascript: history.go(-1)" class="flex items-center mr-4">
            <img class="w-6 mr-2" src="{{ asset('storage/recursos/icons/volver_naranja.png') }}" alt="Volver">
            <p class="text-lg font-semibold text-naranja">Pacientes</p>
        </a>

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
                <!-- botón Editar-->
            @endcan

            <button wire:click="edit" class="flex items-center px-4 py-2 bg-green-500 text-white rounded-lg shadow-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                <span>Editar</span>
            </button>
            <!-- botón Tratamiento-->
            <button wire:click="showTratamientosModal" class="flex items-center px-4 py-2 bg-azul text-white rounded-lg shadow-md hover:bg-teal-500 focus:outline-none focus:ring-2 focus:ring-green-300">
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
                <p class="text-lg font-semibold text-gray-800">Nombre: <span class="text-azul">{{ $paciente->name }}</span></p>
                <p class="text-lg font-semibold text-gray-800">Email: <span class="text-azul">{{ $paciente->email }}</span></p>
                <p class="text-lg font-semibold text-gray-800">Teléfono: <span class="text-azul">{{ $paciente->telefono }}</span></p>
                <p class="text-lg font-semibold text-gray-800">Próxima revisión: <span class="text-azul">{{ date('d/m/Y', strtotime($paciente->revision)) }}</span></p>

                <!-- Cambiar fecha revisión -->
                <div class="flex items-center space-x-4">
                    <input type="date" wire:model="fecha" class="block px-3 py-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
                    <button wire:click="revision" class="text-azul hover:underline">Cambiar fecha revisión</button>
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
                <p class="text-lg font-semibold text-gray-800">Observaciones stripping: <span class="text-azul">{{ $paciente->odontograma_observaciones }}</span></p>
                <p class="text-lg font-semibold text-gray-800">Observaciones: <span class="text-azul">{{ $paciente->observacion }}</span></p>
                <p class="text-lg font-semibold text-gray-800">Comentarios cbct: <span class="text-azul">{{ $paciente->obser_cbct }}</span></p>

                <!-- Añadir observaciones odontograma -->
                @can('doctor_user')
                    <textarea wire:model="obs_odontograma" class="w-full mt-4 p-2 text-gray-600 border border-gray-300 rounded-md shadow-sm" rows="4" placeholder="Añadir observaciones..."></textarea>
                    <button wire:click="ob_odontograma" class="mt-2 text-blue-600 hover:underline">Añadir observaciones odontograma</button>
                @endcan
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
                        <thead class="bg-gray-100">
                            <tr class="border-b">
                                <th class="px-6 py-3 text-left text-gray-600 font-medium">Vista Previa</th>
                                <th class="px-6 py-3 text-center text-gray-600 font-medium">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($imagenes as $imagen)
                                <tr class="border-b hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4">
                                        <a href="{{ Storage::url($imagen->ruta) }}" target="_blank" class="block w-16 h-16">
                                            <img src="{{ Storage::url($imagen->ruta) }}" alt="ImagenPaciente" class="w-full h-full object-cover rounded-md">
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-center flex items-center justify-center space-x-2">
                                        <a href="{{ Storage::url($imagen->ruta) }}" target="_blank" class="text-blue-500 hover:text-blue-700 underline">Ver</a>
                                        <a href="{{ Storage::url($imagen->ruta) }}" download="{{ basename($imagen->ruta) }}" class="text-blue-500 hover:text-blue-700 underline">Descargar</a>
                                        @if(Auth::user()->hasRole('doctor'))
                                            <button wire:click="deleteImage({{ $imagen->id }})" class="text-red-500 hover:text-red-700 underline">Eliminar</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="px-6 py-4">
                                    @if ($imagenes->hasPages())
                                        {{ $imagenes->links('vendor.pagination.paginacion') }}
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
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
                        <tfoot>
                            <tr>
                                <td colspan="2" class="px-4 py-2">
                                    @if ($archivos->hasPages())
                                        {{ $archivos->links('vendor.pagination.paginacion') }}
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- Añadir IMG o ZIP --}}
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

    {{-- Añadir Tratamiento --}}
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

    {{-- Editar Paciente --}}
    @if ($showModalPaciente)
        <x-dialog-modal maxWidth="lg" x-data="{ showModalPaciente: @entangle('showModalPaciente') }">
            <div class="relative">
                <x-slot name="title">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold">
                            {{ 'Editar Paciente '. $paciente->num_paciente }}
                        </h2>
                        <button wire:click='close' class="text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </x-slot>

                <x-slot name="content">
                    <form wire:submit.prevent="save">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2 sm:col-span-1 mb-4">
                                <x-label for="name" value="Nombre*" class="text-azul text-base"/>
                                <x-input type="text" id="name" class="w-full rounded-md" wire:model.defer="name" />
                                <x-input-error for="name" />
                            </div>

                            <div class="col-span-2 sm:col-span-1 mb-4">
                                <x-label for="email" value="Email*" class="text-azul text-base"/>
                                <x-input type="email" id="email" class="w-full rounded-md" wire:model.defer="email"/>
                                <x-input-error for="email" />
                            </div>

                            <div class="col-span-2 sm:col-span-1 mb-4">
                                <x-label for="fecha_nacimiento" value="Fecha Nacimiento*" class="text-azul text-base"/>
                                <x-input type="date" id="fecha_nacimiento" class="w-full rounded-md" wire:model.defer="fecha_nacimiento" />
                                <x-input-error for="fecha_nacimiento" />
                            </div>
                            <div class="col-span-2 sm:col-span-1 mb-4">
                                <x-label for="telefono" value="Teléfono*" class="text-azul text-base"/>
                                <x-input type="text" id="telefono" class="w-full rounded-md" wire:model.defer="telefono"/>
                                <x-input-error for="telefono" />
                            </div>
                            <div class="col-span-2 mb-4">
                                <x-label for="observacion" value="Observaciones" class="text-azul text-md"/>
                                <textarea wire:model.defer="observacion" class="w-full rounded-md border border-[rgb(224,224,224)] resize-none" rows="4" ></textarea>
                                <x-input-error for="observacion" />
                            </div>

                            <div class="col-span-2 mb-4">
                                <x-label for="obser_cbct" value="Observaciones Cbct/Imágenes" class="text-azul text-base"/>
                                <textarea wire:model.defer="obser_cbct" class="w-full rounded-md border border-[rgb(224,224,224)] resize-none" rows="4" placeholder="Definir medididas, intraorales o físicas"></textarea>
                                <x-input-error for="obser_cbct" />
                            </div>
                        </div>
                    </form>
                </x-slot>

                <x-slot name="footer">
                    <spa class="px-4 py-2 text-center">Campos obligatorios (*)</spa>
                    <button type="button" wire:click="close" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                    <button type="submit" wire:click="savePaciente" class="bg-blue-500 text-white px-4 py-2 rounded">
                        {{ 'Actualizar' }}
                    </button>
                </x-slot>
            </div>
        </x-dialog-modal>
    @endif
    <div class="flex items-center justify-between mb-6">
        <!-- Contenedor del título -->
        <a href="javascript: history.go(-1)" class="flex items-center mr-4">
            <img class="w-6 mr-2" src="{{ asset('storage/recursos/icons/volver_naranja.png') }}" alt="Volver">
            <p class="text-lg font-semibold text-naranja">Pacientes</p>
        </a>

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
            <!-- botón Editar-->
            <button wire:click="edit" class="flex items-center px-4 py-2 bg-green-500 text-white rounded-lg shadow-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                <span>Editar</span>
            </button>
            <!-- botón Tratamiento-->
            {{-- <button wire:click="showTratamientosModal" class="flex items-center px-4 py-2 bg-azul text-white rounded-lg shadow-md hover:bg-teal-500 focus:outline-none focus:ring-2 focus:ring-green-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                <span>Añadir Tratamiento</span>
            </button> --}}
        </div>
    </div>
    <div class="bg-gray-100 shadow-md rounded-lg">
        <div class="container mx-auto my-5 p-5">
            <div class="md:flex no-wrap md:-mx-2 ">
                <!-- Left Side -->
                <div class="w-full md:w-3/12 md:mx-2">
                    <!-- Profile Card -->
                    <div class="bg-white p-3 border-t-4 border-green-500">
                        <div class="image overflow-hidden rounded-lg">
                            <img class="h-auto w-full mx-auto"
                                src="https://cdn.australianageingagenda.com.au/wp-content/uploads/2015/06/28085920/Phil-Beckett-2-e1435107243361.jpg"
                                alt="">
                        </div>
                        <h1 class="text-gray-900 font-bold text-xl leading-8 my-1">{{$paciente->name}} {{$paciente->apellidos}}</h1>
                        <h3 class="text-gray-600 font-lg text-semibold leading-6">{{$paciente->clinica->name}}</h3>
                        <ul class="bg-gray-100 text-gray-600 hover:text-gray-700 hover:shadow py-2 px-3 mt-3 divide-y rounded shadow-sm">
                            <li class="flex items-center py-3">
                                <span>Status</span>
                                <span class="ml-auto"><span
                                    class="bg-green-500 py-1 px-2 rounded text-white text-sm">Active</span>
                                </span>
                            </li>
                            <li class="flex items-center py-3">
                                <span>Miembro desde</span>
                                <span class="ml-auto">{{ date('d/m/Y', strtotime($paciente->created_at)) }}</span>
                            </li>
                        </ul>
                    </div>
                    <!-- End of profile card -->
                    <div class="my-4"></div>
                </div>
                <!-- Right Side -->
                <div class="w-full md:w-9/12 mx-2 h-64 rounded-lg">
                    <!-- Profile tab -->
                    <div class="bg-white p-3 shadow-sm rounded-sm ">
                        <div class="flex items-center space-x-2 font-semibold text-gray-900 leading-8">
                            <span clas="text-green-500">
                                <svg class="h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                            <span class="tracking-wide">Datos Paciente</span>
                        </div>
                        <div class="text-gray-700">
                            <div class="grid md:grid-cols-2 text-sm">
                                <div class="grid grid-cols-2">
                                    <div class="px-4 py-2 font-semibold">Nombre</div>
                                    <div class="px-4 py-2 text-azul">{{$paciente->name}}</div>
                                </div>
                                <div class="grid grid-cols-2">
                                    <div class="px-4 py-2 font-semibold">Apellidos</div>
                                    <div class="px-4 py-2 text-azul">{{$paciente->apellidos}}</div>
                                </div>
                                {{-- <div class="grid grid-cols-2">
                                    <div class="px-4 py-2 font-semibold">Gender</div>
                                    <div class="px-4 py-2">Female</div>
                                </div> --}}
                                <div class="grid grid-cols-2">
                                    <div class="px-4 py-2 font-semibold">Teléfono</div>
                                    <div class="px-4 py-2 text-azul">{{$paciente->telefono}}</div>
                                </div>
                                {{-- <div class="grid grid-cols-2">
                                    <div class="px-4 py-2 font-semibold">Current Address</div>
                                    <div class="px-4 py-2">Beech Creek, PA, Pennsylvania</div>
                                </div> --}}
                                {{-- <div class="grid grid-cols-2">
                                    <div class="px-4 py-2 font-semibold">Permanant Address</div>
                                    <div class="px-4 py-2">Arlington Heights, IL, Illinois</div>
                                </div> --}}
                                <div class="grid grid-cols-2">
                                    <div class="px-4 py-2 font-semibold">Email:</div>
                                    <div class="px-4 py-2">
                                        <a class="text-azul" href="mailto:{{$paciente->email}}">{{$paciente->email}}</a>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2">
                                    <div class="px-4 py-2 font-bold">Fecha Nacimiento</div>
                                    <div class="px-4 py-2 text-azul">{{ date('d/m/Y', strtotime($paciente->fecha_nacimiento)) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="my-4"></div>
                    </div>
                    <!-- End of about section -->
                    <div class="my-4"></div>

                    <!-- Experience and education -->
                    <div class="bg-white p-3 shadow-sm rounded-lg">
                        <div class="grid grid-cols-2">
                            <div>
                                <div class="flex items-center space-x-2 font-semibold text-gray-900 leading-8 mb-3">
                                    <span clas="text-green-500">
                                        <svg class="h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </span>
                                    <span class="tracking-wide">Tratamientos</span>
                                </div>
                                <ul class="list-inside space-y-2">
                                    @foreach ($tratamientos as $tratamiento)
                                        <li>
                                            <div class="text-teal-600">{{$tratamiento->tratamiento->name }}</div>
                                            <div class="text-gray-500 text-xs">Fecha Inicio: {{$tratamiento->created_at}}</div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            {{-- ETapas? --}}
                            {{-- <div>
                                <div class="flex items-center space-x-2 font-semibold text-gray-900 leading-8 mb-3">
                                    <span clas="text-green-500">
                                        <svg class="h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path fill="#fff" d="M12 14l9-5-9-5-9 5 9 5z" />
                                            <path fill="#fff"
                                                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" />
                                        </svg>
                                    </span>
                                    <span class="tracking-wide">Education</span>
                                </div>
                                <ul class="list-inside space-y-2">
                                    <li>
                                        <div class="text-teal-600">Masters Degree in Oxford</div>
                                        <div class="text-gray-500 text-xs">March 2020 - Now</div>
                                    </li>
                                    <li>
                                        <div class="text-teal-600">Bachelors Degreen in LPU</div>
                                        <div class="text-gray-500 text-xs">March 2020 - Now</div>
                                    </li>
                                </ul>
                            </div> --}}
                        </div>
                        <!-- End of Experience and education grid -->
                    </div>
                    <!-- End of profile tab -->
                </div>
            </div>
        </div>
    </div>
</div>
