<div>
    {{-- <div class="max-w-6xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="block mb-8">
            <a href="{{ route('users.create') }}" class="bg-green-500 hover:bg-azul text-white font-bold py-2 px-4 rounded">Nueva Clínica</a>
        </div>

        <div class="flex flex-col">
            <div class="-my-2 overflow-x-auto sm:-mx-7 lg:-mx-8">
                <div class="py-3 align-middle inline-block min-w-full sm:px-7 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 w-full">
                            <thead>
                                <tr>
                                    <th scope="col" width="50" class="px-6 py-3 bg-gray-50 text-left text-azul uppercase tracking-wider">
                                        ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-azul uppercase tracking-wider">
                                        Nombre
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-azul uppercase tracking-wider">
                                        Responsable
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-azul uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-azul uppercase tracking-wider">
                                        Teléfono
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-azul uppercase tracking-wider">
                                        Dirección
                                    </th>
                                    <th scope="col" width="200" class="px-6 py-3 bg-gray-50 text-azul">
                                        Acciones
                                    </th>
                                </tr>

                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($clinicas as $clinica)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $clinica->id }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $clinica->name }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $clinica->responsable }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $clinica->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $clinica->telefono }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $clinica->direccion }} - {{ $clinica->localidad }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <!-- Botón de editar -->
                                        <button wire:click="openEditModal({{ $clinica }})" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Botón de eliminar -->
                                        <button wire:click="openDeleteModal({{ $clinica->id }})" class="text-red-600 hover:text-red-900 ml-2">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="px-6 py-4 flex justify-between items-center">
            <div class="flex justify-start items-center">
                <span class="uppercase text-base text-azul font-light">Ordenar por: </span>
                <select name="" id="" wire:model='ordenar' class="px-4 py-2 mt-2 ml-5 text-azul placeholder-gray-400 bg-white border border-azul rounded-none focus:border-none focus:outline-none focus:ring-none focus:ring-none focus:ring-opacity-40">
                    <option value="nombre">A-Z</option>
                    <option value="recientes">Recientes</option>
                    <option value="">Código</option>
                </select>
            </div>
            <div class="flex justify-end">
                <div>
                    <x-secondary-button wire:click="$set('open', true)" class="text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Nueva Clínica</x-secondary-button>
                </div>
                <div class="flex items-center ml-5">
                    <x-input class="text-azul" type="text" wire:model="search" placeholder="Buscar clínica" />
                </div>
            </div>
        </div>
        <x-tabla>

            @if ($clinicas->count())
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="text-white">
                        <tr class="bg-azul">
                            <th scope="col" class="px-6 text-center uppercase">ID</th>
                            <th scope="col" class="p-3 text-center uppercase">Título</th>
                            <th scope="col" class="p-3 text-center uppercase">Responsable</th>
                            <th scope="col" class="p-3 text-center uppercase">Dirección</th>
                            <th scope="col" class="p-3 text-center uppercase">Acción</th>
                        </tr>
                    </thead>

                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($clinicas as $item)
                            <tr class="bg-gray-100 hover:bg-gray-200 transition">
                                <td class="px-6 py-4 text-center border-b">
                                    <div class="text-sm text-gray-900">{{ $item->id }}</div>
                                </td>
                                <td class="px-6 py-4 text-center border-b">
                                    <div class="text-sm text-gray-900">{{ $item->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-center border-b">{{$item->responsable}}</td>
                                <td class="px-6 py-4 text-center border-b">
                                    <div class="text-sm text-gray-900">{{ $item->direccion }} - {{$item->localidad}}</div>
                                </td>
                                <td class="px-6 py-4 text-center font-medium flex">
                                    {{-- <button class="bg-gray-600 hover:bg-blue-500 text-black px-4 py-2 rounded" wire:click="visitar({{$item}})" target="_blank">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </button>
                                    <a class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded" wire:click="edit({{$item}})">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a class="bg-red-600 text-white px-4 py-2 rounded" wire:click="$emit('deletePost', {{$item->id}})">
                                        <i class="fas fa-trash"></i>
                                    </a> --}}
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
    </div>
    {{-- <x-dialog-modal wire:model="open_edit">
        <x-slot name="title">
            Editar Clínica
        </x-slot>

        <x-slot name="content">
            <!-- Formulario de edición -->
            <div class="mt-4">
                <x-label for="name" value="{{ __('Nombre') }}" />
                <x-input id="name" type="text" class="mt-1 block w-full" wire:model="clinica.name" />
                <x-input-error for="name" class="mt-2" />

                <x-label for="responsable" value="{{ __('Responsable') }}" class="mt-4" />
                <x-input id="responsable" type="text" class="mt-1 block w-full" wire:model="clinica.responsable" />
                <x-input-error for="responsable" class="mt-2" />

                <x-label for="email" value="{{ __('Email') }}" class="mt-4" />
                <x-input id="email" type="email" class="mt-1 block w-full" wire:model="clinica.email" />
                <x-input-error for="email" class="mt-2" />

                <x-label for="telefono" value="{{ __('Teléfono') }}" class="mt-4" />
                <x-input id="telefono" type="text" class="mt-1 block w-full" wire:model="clinica.telefono" />
                <x-input-error for="telefono" class="mt-2" />

                <x-label for="direccion" value="{{ __('Dirección') }}" class="mt-4" />
                <x-input id="direccion" type="text" class="mt-1 block w-full" wire:model="clinica.direccion" />
                <x-input-error for="direccion" class="mt-2" />
            </div>

        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('open_edit', false')" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>

            <x-button class="ml-2" wire:click="updateClinica" wire:loading.attr="disabled">
                Guardar
            </x-button>
        </x-slot>
    </x-dialog-modal> --}}
</div>
