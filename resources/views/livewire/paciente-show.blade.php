<div>
    <div class="flex items-center justify-between mb-6">
        <!-- Contenedor del título -->
        <a href="javascript: history.go(-1)" class="flex items-center mr-4">
            <img class="w-6 mr-2" src="{{ asset('storage/recursos/icons/volver_naranja.png') }}" alt="Volver">
            <p class="text-lg font-semibold text-naranja">Pacientes</p>
        </a>

        <!-- Contenedor de los botones -->
        <div class="flex space-x-4">
            <!-- Primer botón -->
            @can('stripping')
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
                                src="{{ asset('storage/'. $paciente->url_img) }}"
                                alt="Foto {{$paciente->name}}">
                        </div>
                        <h1 class="text-gray-900 font-bold text-xl leading-8 my-1">{{$paciente->name}} {{$paciente->apellidos}}</h1>
                        {{-- <h3 class="text-gray-600 font-semibold text-azul leading-6">{{$paciente->clinicas[0]->name}}</h3> --}}
                        <ul class="bg-gray-100 text-gray-600 hover:text-gray-700 hover:shadow py-2 px-3 mt-3 divide-y rounded shadow-sm">
                            <li class="flex items-center py-3">
                                <span>Status</span>
                                <span class="ml-auto">
                                    <button wire:click="toggleActivo"
                                    class="{{ $paciente->activo ? 'bg-green-500' : 'bg-red-500' }} py-1 px-2 rounded text-white text-sm">
                                    {{ $paciente->activo ? 'Activo' : 'Inactivo' }}
                                    </button>
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
                <div class="w-full md:w-9/12 mx-2 rounded-lg">
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
                                <div class="grid grid-cols-2">
                                    <div class="px-4 py-2 font-semibold">Teléfono</div>
                                    <div class="px-4 py-2 text-azul">{{$paciente->telefono}}</div>
                                </div>
                                <div class="grid grid-cols-2">
                                    <div class="px-4 py-2 font-semibold">Email</div>
                                    <div class="px-4 py-2">
                                        <a class="text-azul" href="mailto:{{$paciente->email}}">{{ $paciente->email }}</a>
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

                    <!-- Tratamientos -->
                    <div class="bg-white p-5 shadow-lg rounded-lg space-y-6">
                        <!-- Título de la sección -->
                        <div class="flex justify-evenly items-center space-x-8">
                            <!-- Historial de Tratamientos -->
                            <div class="flex items-center space-x-2 font-semibold text-gray-900">
                                <span class="text-gray-500">
                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </span>
                                <span class="tracking-wide text-xl">Historial de Tratamientos</span>
                            </div>
                        </div>

                        <!-- Contenedor de Tratamientos y Etapas -->
                        <div class="space-y-8">
                            @foreach ($tratamientos as $tratamiento)
                                <div class="grid grid-cols-1 gap-6 items-start border-t pt-4">
                                    <!-- Tratamiento -->
                                    <div class="space-y-2">
                                        <div class="text-teal-600 font-semibold text-lg cursor-pointer" wire:click='historial({{$paciente->id}}, {{$tratamiento->id}})'>
                                            {{ $tratamiento->name }} - {{ $tratamiento->descripcion }}
                                        </div>
                                        <div class="text-gray-500 text-sm">Fecha Inicio: {{ date('d/m/Y', strtotime($tratamiento->created_at)) }}</div>
                                    </div>

                                    <!-- Fases y Etapas -->
                                    <div class="space-y-6 ">
                                        @foreach ($tratamiento->fases as $fase)
                                            <div class="border-t pt-4">
                                                <!-- Fase -->
                                                <div class="flex justify-between items-center">
                                                    <h3 class="text-lg font-semibold text-teal-700">{{ $fase->name }}</h3>
                                                    <span class="text-sm text-gray-600">{{ $fase->created_at->format('d/m/Y') }}</span>
                                                </div>

                                                <!-- Etapas de la fase -->
                                                <ul class="space-y-4 mt-4">
                                                    @foreach ($fase->etapas as $etapa)
                                                    <li class="p-4 rounded-lg shadow-md border
                                                        {{ $etapa->status === 'Finalizado' ? 'bg-red-100 border-red-300' :
                                                            ($etapa->status === 'Pausado' ? 'bg-blue-100 border-blue-300' :
                                                            ($etapa->status === 'En proceso' ? 'bg-green-100 border-green-300' :
                                                            ($etapa->status === 'Set Up' ? 'bg-yellow-100 border-yellow-300' : 'bg-gray-100 border-gray-200'))) }}">
                                                        <div class="flex justify-between items-center">
                                                            <span class="font-semibold text-md">{{ $etapa->name }}</span>
                                                            @if ($etapa->status === 'Finalizado')
                                                                <span class="text-sm text-gray-800">
                                                                    Finalizado el: {{ \Carbon\Carbon::parse($etapa->fecha_fin)->format('d/m/Y') }}
                                                                </span>
                                                            @else
                                                                <span class="text-sm text-gray-600">
                                                                    Próxima revisión: {{ $etapa->revision ? \Carbon\Carbon::parse($etapa->revision)->format('d/m/Y') : 'No asignada' }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="mt-2 text-xs text-gray-500">
                                                            <!-- Estado de la etapa -->
                                                            <div class="mt-2">
                                                                <strong>Estado:</strong>
                                                                <span class="text-sm {{
                                                                    $etapa->status === 'Finalizado' ? 'text-red-600' :
                                                                    ($etapa->status === 'Pausado' ? 'text-blue-600' :
                                                                    ($etapa->status === 'En proceso' ? 'text-green-600' :
                                                                    ($etapa->status === 'Set Up' ? 'text-yellow-600' : 'text-gray-600'))) }}">
                                                                    {{ $etapa->status }}
                                                                </span>
                                                            </div>

                                                            <!-- Detalles adicionales de la etapa -->
                                                            @if($etapa->mensajes->count() > 0)
                                                                <div class="mt-4">
                                                                    <h4 class="text-sm font-semibold text-gray-700">Mensajes:</h4>
                                                                    <ul class="space-y-2 mt-2">
                                                                        @foreach ($etapa->mensajes as $mensaje)
                                                                            <li class="bg-gray-50 p-3 rounded-md border-l-4 border-teal-500 shadow-sm">
                                                                                <div class="flex justify-between items-center">
                                                                                    <span class="text-sm text-gray-800">{{ $mensaje->mensaje }}</span>
                                                                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($mensaje->created_at)->format('d/m/Y H:i') }}</span>
                                                                                </div>
                                                                                <div class="text-xs text-gray-600 mt-2">
                                                                                    <span class="font-semibold">Usuario:</span> {{ $mensaje->user->name }}
                                                                                </div>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @else
                                                                <div class="mt-4 text-sm text-gray-500">No hay mensajes para esta etapa.</div>
                                                            @endif
                                                        </div>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>

                    <div class="my-3"></div>

                    {{-- Observaciones --}}
                    <div class="bg-white p-3 shadow-sm rounded-lg">
                        <div class="grid grid-cols-1">
                            <div>
                                <div class="flex items-center space-x-2 font-semibold text-gray-900 leading-8 mb-3">
                                    <span clas="text-green-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M21 3h-7a2.98 2.98 0 0 0-2 .78A2.98 2.98 0 0 0 10 3H3a1 1 0 0 0-1 1v15a1 1 0 0 0 1 1h5.758a2.01 2.01 0 0 1 1.414.586l1.121 1.121c.009.009.021.012.03.021.086.08.182.15.294.196h.002a.996.996 0 0 0 .762 0h.002c.112-.046.208-.117.294-.196.009-.009.021-.012.03-.021l1.121-1.121A2.01 2.01 0 0 1 15.242 20H21a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zm-1 15h-4.758a4.03 4.03 0 0 0-2.242.689V6c0-.551.448-1 1-1h6v13z"></path></svg>
                                    </span>
                                    <span class="tracking-wide">Observaciones</span>
                                </div>
                                <ul class="list-inside space-y-2">
                                    <li>
                                        <span class="font-semibold">Observación General:</span>
                                        <span class="text-azul">{{$paciente->observacion ? $paciente->observacion : "No hay ninguna observación"}}</span>
                                    </li>
                                    <li>
                                        <span class="font-semibold">Observación CBCT:</span>
                                        <span class="text-azul">{{$paciente->obser_cbct ? $paciente->obser_cbct : "No hay ninguna observación CBCT"}}</span>
                                    </li>
                                    <li>
                                        <span class="font-semibold">Observación Odontograma:</span>
                                        <span class="text-azul">{{$paciente->odontograma_obser ? $paciente->odontograma_obser : "No hay ninguna observación"}}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- End of profile tab -->
                    <div class="my-4"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Añadir Stripping--}}
    {{-- @if ($showModal)
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
    @endif --}}

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
                                <x-label for="apellidos" value="Apellidos*" class="text-azul text-base"/>
                                <x-input type="text" id="apellidos" class="w-full rounded-md" wire:model.defer="apellidos" />
                                <x-input-error for="apellidos" />
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
                            {{-- <div class="col-span-2 mb-4">
                                <x-label for="img_paciente" class="block text-md text-azul capitalize">Foto paciente</x-label>

                                @if($url_img)
                                    <!-- Mostrar imagen si ya existe -->
                                    <div class="mb-4">
                                        <img src="{{ asset('storage/' . $url_img) }}" alt="Foto de Paciente" class="w-32 h-32 object-cover rounded-md">
                                    </div>
                                    <button type="button" wire:click="$set('url_img', null)" class="text-sm text-red-600">Cambiar imagen</button>
                                @else
                                    <!-- Input para subir nueva imagen si no existe o si se decidió cambiar -->
                                    <x-input wire:model="img_paciente" type="file" class="block w-full px-3 py-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-300 focus:ring-opacity-40" />
                                    <x-input-error for="img_paciente" />
                                @endif
                            </div> --}}

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
                            <div class="col-span-2 mb-4">
                                <x-label for="odontograma" value="Observaciones Odontograma" class="text-azul text-base"/>
                                <textarea wire:model.defer="odontograma" class="w-full rounded-md border border-[rgb(224,224,224)] resize-none" rows="4" placeholder="Definir medididas, intraorales o físicas"></textarea>
                                <x-input-error for="odontograma" />
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
</div>
