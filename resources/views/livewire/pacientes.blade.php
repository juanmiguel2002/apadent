<div>
    @can('paciente_view')
        <div class="px-6 py-4 flex justify-between items-center">
            <div class="flex justify-start items-center">
                <span class="uppercase text-base text-azul font-light">Ordenar por: </span>
                <select wire:model="ordenar" id=""
                    class="px-4 py-2 mt-2 ml-5 text-azul placeholder-gray-400 bg-white border border-azul rounded-none focus:border-none focus:outline-none focus:ring-none focus:ring-none focus:ring-opacity-40">
                    <option value="name">A-Z</option>
                    <option value="recientes">Recientes</option>
                    <option value="">Código</option>
                </select>
            </div>
            <div class="flex justify-end">
                <button wire:click="showCreateModal" class="bg-azul text-white px-2 py-1 rounded ml-2">Crear Paciente</button>
                <div class="flex items-center ml-5">
                    <x-input class="text-azul" type="text" wire:model.live="search" placeholder="Buscar paciente" />
                </div>
            </div>
        </div>

        <x-tabla>
            @if ($pacientes->count())
                <table class="min-w-full divide-y table-fixed">
                    <thead class="text-white">
                        <tr class="bg-azul">
                            <th class="px-6 text-center">Cód Paciente</th>
                            <th class="p-3 text-center">Nombre</th>
                            <th class="p-3 text-center">Tratamiento</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Nº Etapa</th>
                            <th class="p-3 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-200 ">
                        @foreach($pacientes as $paciente)
                            <tr>
                                <td class="text-center px-4 py-2">{{ $paciente->num_paciente }}</td>
                                <td class="text-center px-4 py-2 cursor-pointer" wire:click='showPaciente({{$paciente->id}})'>{{ $paciente->name }}</td>
                                <td class="text-center px-4 py-2">{{ $paciente->tratamiento_nombre }}</td>
                                @foreach (['En proceso' => 'bg-green-600', 'Pausado' => 'bg-blue-600', 'Finalizado' => 'bg-red-600', 'Set Up' => 'bg-yellow-600'] as $status => $color)
                                    @if ($paciente->tratamiento_status == $status)
                                        <td class="p-3 text-center flex justify-center items-center h-full mt-4">
                                            <button wire:click="toggleMenu" class="flex items-center justify-center px-6 text-white {{ $color }} font-medium rounded-xl">
                                                <span>{{ $status }}</span>
                                            </button>
                                            <img class="ml-2 w-3" src="{{ asset('storage/recursos/icons/flecha_abajo.png') }}" alt="">

                                            @if ($mostrar)
                                                <div class="ml-4">
                                                    @foreach ($statuses as $optionStatus => $optionColor)
                                                        <div wire:click="estado({{ $paciente->trat_id }}, '{{ $optionStatus }}')"
                                                            class="cursor-pointer text-white {{ $optionColor }} my-2 rounded-lg hover:bg-opacity-75 px-2">
                                                            {{ $optionStatus }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                @endforeach
                                <td class="text-center px-4 py-2">{{ $paciente->tratamiento_status }}</td>
                                <td class="text-center border px-4 py-2">
                                    <button wire:click="edit({{ $paciente->id }})"
                                        class="bg-yellow-500 text-white px-2 py-1 rounded">Editar</button>
                                    <button wire:click="showHistorial({{ $paciente->trat_id }})"
                                        class="bg-green-500 text-white px-2 py-1 rounded">Historial</button>
                                    @can('paciente_delete')
                                    <button wire:click="delete({{ $paciente->id }})"
                                        class="bg-red-500 text-white px-2 py-1 rounded">Eliminar</button>
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
                {{ $pacientes->links('vendor.livewire.paginacion') }}
            @endif
        </x-tabla>

        {{-- Editar o Añadir paciente --}}
        @can('paciente_create')
            @if($showModal)
                <x-dialog-modal id="" maxWidth="xl" x-data="{ showModal: @entangle('showModal') }">
                    <div class="relative">
                        <x-slot name="title">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-bold">
                                    {{ $isEditing ? 'Editar Paciente' : 'Añadir Paciente' }}
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
                                    <div class="col-span-2 mb-4">
                                        <x-label for="num_paciente" value="Número Paciente*" class="text-azul text-base"/>
                                        <x-input type="text" id="num_paciente" class="w-full rounded-md" wire:model.defer="num_paciente" placeholder="2002"/>
                                        <x-input-error for="num_paciente" />
                                    </div>

                                    <div class="col-span-2 sm:col-span-1 mb-4">
                                        <x-label for="name" value="Nombre*" class="text-azul text-base"/>
                                        <x-input type="text" id="name" class="w-full rounded-md" wire:model.defer="name" placeholder="Strat" />
                                        <x-input-error for="name" />
                                    </div>

                                    <div class="col-span-2 sm:col-span-1 mb-4">
                                        <x-label for="email" value="Email*" class="text-azul text-base"/>
                                        <x-input type="email" id="email" class="w-full rounded-md" wire:model.defer="email" placeholder="strat@gmail.com" />
                                        <x-input-error for="email" />
                                    </div>

                                    <div class="col-span-2 sm:col-span-1 mb-4">
                                        <x-label for="fecha_nacimiento" value="Fecha Nacimiento*" class="text-azul text-base"/>
                                        <x-input type="date" id="fecha_nacimiento" class="w-full rounded-md" wire:model.defer="fecha_nacimiento" />
                                        <x-input-error for="fecha_nacimiento" />
                                    </div>
                                    <div class="col-span-2 sm:col-span-1 mb-4">
                                        <x-label for="telefono" value="Teléfono*" class="text-azul text-base"/>
                                        <x-input type="text" id="telefono" class="w-full rounded-md" wire:model.defer="telefono" placeholder="978456123"/>
                                        <x-input-error for="telefono" />
                                    </div>
                                    @if ($isEditing)
                                        @can('doctor_user')
                                            <div class="col-span-2 sm:col-span-1 mb-4">
                                                <x-label for="tratEtapa_id" value="tratEtapa*" class="text-azul text-base"/>
                                                <select name="tratamiento_id" wire:model="selectedTratamiento" class="form-input block w-full rounded-md border border-[rgb(224,224,224)]">
                                                    <option value="">Seleccione un Tratamiento</option>
                                                    @foreach($tratamientos as $tratamiento)
                                                        <option value="{{ $tratamiento->id }}">{{ $tratamiento->name }}</option>
                                                    @endforeach
                                                </select>
                                                <x-input-error for="tratamiento_id" />
                                            </div>
                                        @endcan
                                    @else
                                        <div class="col-span-2 sm:col-span-1 mb-4">
                                            <x-label for="tratamiento_id" value="Tratamiento*" class="text-azul text-base"/>
                                            <select name="tratamiento_id" wire:model="selectedTratamiento" class="form-input block w-full rounded-md border border-[rgb(224,224,224)]">
                                                <option value="">Seleccione un Tratamiento</option>
                                                @foreach($tratamientos as $tratamiento)
                                                    <option value="{{ $tratamiento->id }}">{{ $tratamiento->name }}</option>
                                                @endforeach
                                            </select>
                                            <x-input-error for="tratamiento_id" />
                                        </div>
                                    @endif

                                    <div class="col-span-2 sm:col-span-1 mb-4">
                                        <x-label for="revision" value="Revisión*" class="text-azul text-base"/>
                                        <x-input type="date" id="revision" class="w-full rounded-md" wire:model.defer="revision" />
                                        <x-input-error for="revision" />
                                    </div>

                                    <div class="col-span-2 mb-4">
                                        <x-label for="observacion" value="Observaciones" class="text-azul text-md"/>
                                        <textarea wire:model.defer="observacion" class="w-full rounded-md border border-[rgb(224,224,224)] resize-none" rows="4" ></textarea>
                                        <x-input-error for="observacion" />
                                    </div>
                                    @if (!$isEditing)
                                        <div class="col-span-2 mb-3">
                                            <x-label for="imagenes" class="block text-md text-azul capitalize">Imágenes</x-label>
                                            <x-input wire:model="imagenes" multiple id="imagenes" name="imagenes" type="file" class="block w-full px-3 py-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-300 focus:ring-opacity-40" />
                                            <x-input-error for="imagenes*" />
                                        </div>

                                        <div class="col-span-2 mb-4">
                                            <x-label for="cbct" class="block text-md text-azul capitalize">Archivos CBCT <i>(comprimidos .zip)</i></x-label>
                                            <x-input multiple wire:model="cbct" id="cbct" name="cbct" type="file" class="block w-full px-3 py-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-300 focus:ring-opacity-40" />
                                            <x-input-error for="cbct.*" />
                                        </div>
                                    @endif

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
                            <button type="submit" wire:click="save" class="bg-blue-500 text-white px-4 py-2 rounded">
                                {{ $isEditing ? 'Actualizar' : 'Crear' }}
                            </button>
                        </x-slot>
                    </div>
                </x-dialog-modal>
            @endif
        @endcan
    @endcan
</div>
