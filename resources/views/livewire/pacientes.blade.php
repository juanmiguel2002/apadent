<div>
    @can('paciente_view')
        <div class="px-4 py-4 flex justify-between items-center">
            <div class="flex justify-start items-center">
                <span class="uppercase text-base text-azul font-light">Ordenar por: </span>
                <select wire:model.live="ordenar"
                    class="px-4 py-2 mt-2 ml-5 text-azul placeholder-gray-400 bg-white border border-azul rounded-none focus:border-none focus:outline-none focus:ring-none focus:ring-none focus:ring-opacity-40">
                    <option value="name">A-Z</option>
                    <option value="recientes">Recientes</option>
                    <option value="">Código</option>
                </select>
            </div>
            <div>
                Mostrar
                <select class="px-5 py-1 text-base text-gray-700 placeholder-gray-400 bg-white border border-transparent border-gray-400 rounded-lg shadow-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent "
                    wire:model.live="perPage">
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="150">150</option>
                </select>
                Registros
            </div>
            <div class="flex justify-end items-center space-x-4">
                @can('paciente_create')
                    <button wire:click="showCreateModal"
                            class="bg-azul hover:bg-azul-dark text-white font-semibold px-4 py-2 rounded shadow">
                        Crear Paciente
                    </button>
                @endcan

                <div class="flex items-center">
                    <x-input class="text-azul" type="text"
                        wire:model.live="search"
                        placeholder="Buscar paciente" />
                </div>

                @can('doctor_user')
                    <button wire:click="toggleVerInactivos"
                            class="px-4 py-2 rounded shadow font-semibold text-white
                                {{ $activo ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600' }}">
                        {{ $activo ? 'Ver Activos' : 'Ver Inactivos' }}
                    </button>
                @endcan
            </div>
        </div>

        <x-tabla>
            @if ($pacientes->count())
                <table class="min-w-full divide-y table-fixed">
                    <thead class="text-white">
                        <tr class="bg-azul">
                            {{-- <th class="p-3 text-center">ID</th> --}}
                            <th class="p-3 text-center">Cód Paciente</th>
                            <th class="p-3 text-center">Nombre y Apellido</th>
                            <th class="p-3 text-center">Teléfono</th>
                            <th class="p-3 text-center">Tratamiento</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Nº de Fase</th>
                            <th class="p-3 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-200 ">
                        @foreach($pacientes as $paciente)
                            <tr>
                                {{-- <td class="text-center px-4 py-2">{{ $paciente->id }}</td> --}}
                                <td class="text-center px-4 py-2">{{ $paciente->num_paciente }}</td>
                                <td class="text-center px-4 py-2 cursor-pointer" wire:click='showPaciente({{$paciente->id}})'>{{ $paciente->name . " " . $paciente->apellidos }}</td>
                                <td class="text-center px-4 py-2">{{ $paciente->telefono }}</td>

                                <td class="text-center px-4 py-2">{{ $paciente->tratamiento_name }} - {{ $paciente->tratamiento_descripcion }}</td>
                                @foreach (['En proceso' => 'bg-green-600', 'Pausado' => 'bg-blue-600', 'Finalizado' => 'bg-red-600', 'Set Up' => 'bg-yellow-600'] as $status => $color)
                                    @if ($paciente->etapa_status == $status)
                                        <td class="p-3 text-center flex justify-center items-center mt-4">
                                            <button wire:click="toggleMenu" class="flex items-center justify-center px-6 text-white {{ $color }} font-medium rounded-xl">
                                                <span>{{ $status }}</span>
                                            </button>
                                            <img class="ml-2 w-3" src="{{ asset('storage/recursos/icons/flecha_abajo.png') }}" alt="flecha_abajo">

                                            @if ($mostrar)
                                                <div class="ml-2">
                                                    @foreach ($statuses as $optionStatus => $optionColor)
                                                        <div wire:click="estado({{ $paciente->id}}, {{ $paciente->tratamiento_id }}, '{{ $optionStatus }}')"
                                                            class="cursor-pointer text-white {{ $optionColor }} my-2 rounded-lg hover:bg-opacity-75 px-2">
                                                            {{ $optionStatus }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                @endforeach
                                <td class="text-center px-4 py-2">{{ $paciente->etapa_name }}</td>
                                <td class="text-center border px-4 py-2">
                                    <button wire:click="showHistorial({{ $paciente->id }})">
                                        <img src="{{ asset('storage/recursos\icons\ojo_azul.png') }}" class="w-5 cursor-pointer">
                                    </button>
                                    @can('paciente_delete')
                                        <button wire:click="deletePaciente({{ $paciente->id }})">
                                            <img class="w-5 cursor-pointer" src="{{ asset('storage/recursos/icons/papelera.png') }}" alt="" srcset="">
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="px-6 py-4">
                    No existe ningún registro coincidente
                </div>
            @endif
            @if ($pacientes->hasPages())
                {{ $pacientes->links('vendor.pagination.paginacion') }}
            @endif
        </x-tabla>

        {{-- Añadir paciente --}}
        @can('paciente_create')
            @if($showModal)
                <x-dialog-modal maxWidth="xl" x-data="{ showModal: @entangle('showModal') }">
                    <div class="relative">
                        <x-slot name="title">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-bold">
                                    {{ 'Añadir Paciente' }}
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
                                @csrf
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-2 mb-4">
                                        <x-label for="num_paciente" value="Número Paciente*" class="text-azul text-base"/>
                                        <x-input type="text" id="num_paciente" class="w-full rounded-md" wire:model="num_paciente" placeholder="2002"/>
                                        <x-input-error for="num_paciente" />
                                    </div>

                                    <div class="col-span-2 sm:col-span-1 mb-4">
                                        <x-label for="name" value="Nombre*" class="text-azul text-base"/>
                                        <x-input type="text" id="name" class="w-full rounded-md" wire:model.defer="name" placeholder="Strat" />
                                        <x-input-error for="name" />
                                    </div>
                                    <div class="col-span-2 sm:col-span-1 mb-4">
                                        <x-label for="name" value="Apellidos*" class="text-azul text-base"/>
                                        <x-input type="text" id="name" class="w-full rounded-md" wire:model.defer="apellidos" placeholder="clinica" />
                                        <x-input-error for="apellidos" />
                                    </div>

                                    <div class="col-span-2 sm:col-span-1 mb-4">
                                        <x-label for="email" value="Email*" class="text-azul text-base"/>
                                        <x-input type="email" id="email" class="w-full rounded-md" wire:model="email" placeholder="strat@gmail.com" />
                                        <x-input-error for="email" />
                                    </div>

                                    <div class="col-span-2 sm:col-span-1 mb-4">
                                        <x-label for="fecha_nacimiento" value="Fecha Nacimiento*" class="text-azul text-base"/>
                                        <x-input type="date" id="fecha_nacimiento" class="w-full rounded-md" wire:model="fecha_nacimiento" />
                                        <x-input-error for="fecha_nacimiento" />
                                    </div>
                                    <div class="col-span-2 sm:col-span-1 mb-4">
                                        <x-label for="telefono" value="Teléfono*" class="text-azul text-base"/>
                                        <x-input type="text" id="telefono" class="w-full rounded-md" wire:model="telefono" placeholder="978456123"/>
                                        <x-input-error for="telefono" />
                                    </div>

                                    <div class="col-span-2 sm:col-span-1 mb-4">
                                        <x-label for="tratamiento_id" value="Tratamiento*" class="text-azul text-base"/>
                                        <select name="tratamiento_id" wire:model="selectedTratamiento" class="form-input block w-full rounded-md border border-[rgb(224,224,224)]">
                                            <option value="">Seleccione un Tratamiento</option>
                                            @foreach($tratamientos as $tratamiento)
                                                <option value="{{ $tratamiento->id }}">{{ $tratamiento->name }} - {{ $tratamiento->descripcion}}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error for="tratamiento_id" />
                                    </div>

                                    <div class="col-span-2 mb-4">
                                        <x-label for="observacion" value="Observaciones" class="text-azul text-md"/>
                                        <textarea wire:model="observacion" class="w-full rounded-md border border-[rgb(224,224,224)] resize-none" rows="4" ></textarea>
                                        <x-input-error for="observacion" />
                                    </div>
                                    <div class="col-span-2 mb-3">
                                        <x-label for="img_paciente" class="block text-md text-azul capitalize">Foto paciente</x-label>
                                        <x-input wire:model="img_paciente" type="file" class="block w-full px-3 py-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-300 focus:ring-opacity-40" />
                                        <x-input-error for="img_paciente" />
                                    </div>
                                    <div class="col-span-2 mb-3">
                                        <x-label for="imagenes" class="block text-md text-azul capitalize">Imágenes</x-label>
                                        <x-input wire:model="imagenes" multiple type="file" class="block w-full px-3 py-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-300 focus:ring-opacity-40" />
                                        {{-- <x-input-error for="imagenes*" /> --}}
                                    </div>

                                    <div class="col-span-2 mb-4">
                                        <x-label for="cbct" class="block text-md text-azul capitalize">Archivos CBCT <i>(comprimidos .zip)</i></x-label>
                                        <x-input multiple wire:model="cbct" type="file" class="block w-full px-3 py-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-300 focus:ring-opacity-40" />
                                        {{-- <x-input-error for="cbcts.*" /> --}}
                                    </div>

                                    <div class="col-span-2 mb-4">
                                        <x-label for="obser_cbct" value="Observaciones Cbct/Imágenes" class="text-azul text-base"/>
                                        <textarea wire:model="obser_cbct" class="w-full rounded-md border border-[rgb(224,224,224)] resize-none" rows="4" placeholder="Definir medididas, intraorales o físicas"></textarea>
                                        <x-input-error for="obser_cbct" />
                                    </div>
                                </div>
                            </form>
                        </x-slot>

                        <x-slot name="footer">
                            <span class="px-4 py-2 text-center">Campos obligatorios (*)</span>
                            <button type="button" wire:click="close" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                            <button type="submit" wire:click="save" class="bg-blue-500 text-white px-4 py-2 rounded">
                                {{ 'Crear' }}
                            </button>
                        </x-slot>
                    </div>
                </x-dialog-modal>
            @endif
        @endcan
    @endcan
</div>
