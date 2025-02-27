<div>
    <div class="px-2 py-4 flex justify-between items-center">
        <div class="flex">
            @if (!auth()->user()->hasRole('admin'))
                <strong>Tratamientos asignados a la clínica {{$clinica[0]->name}}</strong>
            @endif
        </div>
        <div class="flex">
            <button wire:click="showCreateModal" class="bg-azul text-white px-3 py-2 rounded">
                <i class="fas fa-plus mr-2"></i> Crear Tratamiento
            </button>
        </div>
    </div>
    <x-tabla>
        @if ($tratamientos->count())
            <table class="min-w-full divide-y table-fixed">
                <thead class="text-white">
                    <tr class="bg-azul">
                        <th class="p-3 text-center">ID</th>
                        <th class="p-3 text-center">Nombre</th>
                        <th class="p-3 text-center">Descripción</th>
                        <th class="p-3 text-center">Pacientes</th>
                        <th class="p-3 text-center">Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-100 divide-y divide-gray-300">
                    @foreach($tratamientos as $tratamiento)
                        <tr class="hover:bg-gray-200 transition duration-200">
                            <td class="text-center px-4 py-3 whitespace-nowrap">{{ $tratamiento->id }}</td>
                            <td class="text-center px-4 py-3 whitespace-nowrap">{{ $tratamiento->name }}</td>
                            <td class="text-center px-4 py-3 whitespace-nowrap">{{ $tratamiento->descripcion }}</td>
                            <td class="text-center px-4 py-3 whitespace-nowrap">{{ $this->pacientesTrat($tratamiento->id) }}</td>

                            <td class="px-6 py-4 text-sm text-center whitespace-nowrap">
                                <div class="flex justify-center space-x-2">
                                    <a wire:click="showCreateModal({{ $tratamiento->id }})"
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                                        <i class="fas fa-edit mr-2"></i> Editar
                                    </a>
                                </div>
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
    </x-tabla>

    @if ($showModal)
        <x-dialog-modal maxWidth="lg">
            <div class="relative">
                <x-slot name="title">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold">
                            {{ $isEditing ? ' Editar Tratamiento' : 'Crear Tratamiento' }}
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
                        <div class="grid grid-cols-1 gap-4">
                            <div class="col-span-2 mb-4 sm:col-span-1">
                                <x-label for="name" value="Nombre*" class="text-azul text-base"/>
                                <x-input type="text" class="w-full rounded-md" wire:model.defer="name" placeholder="Nombre" />
                                <x-input-error for="name" />
                            </div>

                            <div class="col-span-2 mb-4 sm:col-span-1">
                                <x-label for="descripcion" value="Descripción*" class="text-azul text-base"/>
                                <x-input type="text" class="w-full rounded-md" wire:model.defer="descripcion" placeholder="Descripción" />
                                <x-input-error for="descripcion" />
                            </div>
                        </div>
                    </form>
                </x-slot>

                <x-slot name="footer">
                    <span class="px-4 py-2 text-center">Campos obligatorios (*)</span>
                    <button type="button" wire:click="close" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                    <button type="submit" wire:click="save" class="bg-blue-500 text-white px-4 py-2 rounded">
                        {{ $isEditing ? 'Actualizar' : 'Crear' }}
                    </button>
                </x-slot>
            </div>
        </x-dialog-modal>
    @endif
</div>
