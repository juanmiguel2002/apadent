<div>
    @can('paciente_view')
        <div class="px-6 py-4 flex justify-between items-center">
            <div class="flex justify-start items-center">
                <span class="uppercase text-base text-azul font-light">Ordenar por: </span>
                <select wire:model='ordenar' id=""
                    class="px-4 py-2 mt-2 ml-5 text-azul placeholder-gray-400 bg-white border border-azul rounded-none focus:border-none focus:outline-none focus:ring-none focus:ring-none focus:ring-opacity-40">
                    <option value="name">A-Z</option>
                    <option value="recientes">Recientes</option>
                    <option value="">Código</option>
                </select>
            </div>
            <div class="flex justify-end">
                <button wire:click="showCreateModal" class="bg-azul text-white px-2 py-1 rounded ml-2">Crear Paciente</button>
                <div class="flex items-center ml-5">
                    <x-input class="text-azul" type="text" wire:model="search" placeholder="Buscar clínica" />
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
                            <th class="p-3 text-center">Estado</th>
                            <th class="p-3 text-center">Nº Etapa</th>
                            <th class="p-3 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-200 ">
                        @foreach($pacientes as $paciente)

                            @foreach($paciente->tratEtapas as $tratEtapa)
                                <tr>
                                    <td class="text-center px-4 py-2">{{ $paciente->num_paciente }}</td>
                                    <td class="text-center px-4 py-2" wire:click='showPaciente({{$paciente}})'>{{ $paciente->name }}</td>
                                    <td class="text-center px-4 py-2">{{ $tratEtapa->name }}</td>
                                    <td></td>
                                    <td class="text-center px-4 py-2">{{ $tratEtapa->pivot->status}}</td>
                                    <td class="text-center border px-4 py-2">
                                        <button wire:click="edit({{ $paciente->id }})"
                                            class="bg-yellow-500 text-white px-2 py-1 rounded">Editar</button>
                                        <button wire:click="showHistorial({{ $tratEtapa->id }})"
                                            class="bg-green-500 text-white px-2 py-1 rounded">Historial</button>
                                        @can('paciente_delete')
                                        <button wire:click="delete({{ $paciente->id }})"
                                            class="bg-red-500 text-white px-2 py-1 rounded">Eliminar</button>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach

                    </tbody>
                </table>
            @else
                <div class="px-6 py-4">
                    No existe ningún registro coincidente
                </div>
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
                                        <x-label for="email" value="Email" class="text-azul text-base"/>
                                        <x-input type="email" id="email" class="w-full rounded-md" wire:model.defer="email" placeholder="strat@gmail.com" />
                                        <x-input-error for="email" />
                                    </div>

                                    <div class="col-span-2 sm:col-span-1 mb-4">
                                        <x-label for="fecha_nacimiento" value="Fecha Nacimiento*" class="text-azul text-base"/>
                                        <x-input type="date" id="fecha_nacimiento" class="w-full rounded-md" wire:model.defer="fecha_nacimiento" />
                                        <x-input-error for="fecha_nacimiento" />
                                    </div>
                                    <div class="col-span-2 sm:col-span-1 mb-4">
                                        <x-label for="telefono" value="Teléfono" class="text-azul text-base"/>
                                        <x-input type="text" id="telefono" class="w-full rounded-md" wire:model.defer="telefono" placeholder="978456123"/>
                                        <x-input-error for="telefono" />
                                    </div>
                                    @if ($isEditing)
                                        @can('doctor_user')
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
                                        <x-label for="revision" value="Revisión" class="text-azul text-base"/>
                                        <x-input type="date" id="revision" class="w-full rounded-md" wire:model.defer="revision" />
                                        <x-input-error for="revision" />
                                    </div>

                                    <div class="col-span-2 mb-4">
                                        <x-label for="observaciones" value="Observaciones" class="text-azul text-base"/>
                                        <textarea class="w-full rounded-md border border-[rgb(224,224,224)] resize-none" rows="4" wire:model.defer="observaciones" ></textarea>
                                        <x-input-error for="observaciones" />
                                    </div>

                                    <div class="col-span-2 mb-4">
                                        <x-label for="obser_cbct" value="Observaciones Cbct/Imágenes" class="text-azul text-base"/>
                                        <textarea class="w-full rounded-md border border-[rgb(224,224,224)] resize-none" rows="4" wire:model.defer="obser_cbct" placeholder="Definir medididas, intraorales o físicas"></textarea>
                                        <x-input-error for="obser_cbct" />
                                    </div>
                                </div>
                            </form>
                        </x-slot>

                        <x-slot name="footer">
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
