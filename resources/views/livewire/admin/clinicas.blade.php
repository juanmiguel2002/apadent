<div>
    @include('components.alert-message')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="px-6 py-4 flex justify-between items-center">
            <div class="flex justify-start items-center">
                <span class="uppercase text-base text-azul font-light">Ordenar por: </span>
                <select wire:model.live='ordenar' class="px-4 py-2 mt-2 ml-5 text-azul placeholder-gray-400 bg-white border border-azul rounded-none focus:border-none focus:outline-none focus:ring-none focus:ring-none focus:ring-opacity-40">
                    <option value="name">A-Z</option>
                    <option value="recientes">Recientes</option>
                    <option value="">Código</option>
                </select>
            </div>
            <div class="flex justify-end">
                <button wire:click="showCreateModal" class="bg-azul text-white px-2 py-1 rounded ml-2">Crear Clínica</button>
                <div class="flex items-center ml-5">
                    <x-input class="text-azul" type="text" wire:model.live="search" placeholder="Buscar clínica" />
                </div>
            </div>
        </div>

        <x-tabla>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="text-white">
                    <tr class="bg-azul">
                        <th scope="col" class="p-3 text-center uppercase">ID</th>
                        <th scope="col" class="p-3 text-center uppercase">Nombre</th>
                        <th scope="col" class="p-3 text-center uppercase">Teléfono</th>
                        <th scope="col" class="p-3 text-center uppercase">Usuarios</th>
                        <th scope="col" class="p-3 text-center uppercase">Clientes</th>
                        <th scope="col" class="p-3 text-center uppercase">Dirección</th>
                        <th scope="col" class="p-3 text-center uppercase">Acción</th>
                    </tr>
                </thead>
                @if ($clinicas->count())
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($clinicas as $clinica)
                            <tr class="bg-gray-100 hover:bg-gray-200 transition">
                                <td class="px-6 py-4 text-center border-b">
                                    <div class="text-sm text-gray-900">{{ $clinica->id }}</div>
                                </td>
                                <td class="px-6 py-4 text-center border-b">
                                    <a href="{{ route('admin.clinica.view', ['id' => $clinica->id]) }}">
                                        {{ $clinica->name }}
                                    </a>
                                    <div class="text-lg text-gray-900 cursor-pointer" wire:click='showClinica({{ $clinica->id }})'>

                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center border-b">{{ $clinica->telefono }}</td>
                                <td class="px-6 py-4 text-center border-b">
                                    @php
                                        $user = $clinica->users()->with('roles')->whereHas('roles', function($query) {
                                            $query->where('name', 'doctor_admin'); // Filtrar por rol "doctor_admin"
                                        })->first();
                                    @endphp
                                    @if($clinica->users->isEmpty())
                                        <p>No hay usuarios asignados a esta clínica.</p>
                                    @else
                                        <div title="Usuario admin">{{ $user->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center border-b">
                                    {{ $clinica->pacientes->count() }}
                                </td>
                                <td class="px-6 py-4 text-center border-b">
                                    <div class="text-sm text-gray-900">{{ $clinica->direccion }}</div>
                                </td>
                                <td class="px-6 py-4 text-center font-medium">
                                    <div class="flex justify-center space-x-2">
                                        <a class="text-azul hover:bg-gray-200 rounded cursor-pointer" wire:click="showCreateModal({{ $clinica->id }})" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @role('admin')
                                            <form action="{{ route('admin.clinica.delete', $clinica->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta clínica?')">
                                                @csrf
                                                <button type="submit">
                                                    <img src="{{ asset('storage/recursos/icons/papelera.png') }}" class="w-5 cursor-pointer">
                                                </button>
                                            </form>
                                        @endrole
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                @else
                    <tr class="text-center text-gray-700 bg-gray-100 border border-gray-300 rounded-md shadow-sm">
                        <td class="font-medium px-6 py-4 mt-4" colspan="{{auth()->user()->hasRole('admin') ? '8': '7' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 inline-block mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a8 8 0 11-16 0 8 8 0 0116 0z" />
                            </svg>
                            No existe ningún registro.
                        </td>
                    </tr>
                @endif
            </table>
        </x-tabla>
        <div class="mt-4">
            {{ $clinicas->links('vendor.pagination.paginacion') }}
        </div>
    </div>

    @if($showModal)
        <x-dialog-modal maxWidth="xl" wire:model="showModal">
            <div class="relative">
                <x-slot name="title">
                    <div class="flex justify-between clinicas-center">
                        <h2 class="text-xl font-bold">
                            {{ $editable ? 'Editar Clínica' : 'Añadir Clínica' }}
                        </h2>
                        <button wire:click='close' class="text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </x-slot>

                <x-slot name="content">
                    <form wire:submit="save">
                        @csrf
                        <div class="grid grid-cols-1 gap-4">
                            <div class="col-span-2 sm:col-span-1 mb-4">
                                <x-label for="name" value="Nombre*" class="text-azul text-base"/>
                                <x-input type="text" id="name" class="w-full rounded-md" wire:model.defer="name" placeholder="Nombre Clínica" />
                                <x-input-error for="name" />
                            </div>
                            <div class="col-span-2 sm:col-span-1 mb-4">
                                <x-label for="name" value="Dirección*" class="text-azul text-base"/>
                                <x-input type="text" id="name" class="w-full rounded-md" wire:model.defer="direccion" placeholder="dirección" />
                                <x-input-error for="direccion" />
                            </div>

                            <div class="col-span-2 sm:col-span-1 mb-4">
                                <x-label for="email" value="Email*" class="text-azul text-base"/>
                                <x-input type="email" id="email" class="w-full rounded-md" wire:model="email" placeholder="strat@gmail.com" />
                                <x-input-error for="email" />
                            </div>

                            <div class="col-span-2 sm:col-span-1 mb-4">
                                <x-label for="telefono" value="Teléfono*" class="text-azul text-base"/>
                                <x-input type="tel" id="telefono" class="w-full rounded-md" wire:model="telefono" placeholder="978456123"/>
                                <x-input-error for="telefono" />
                            </div>

                            <div class="col-span-2 sm:col-span-1 mb-4">
                                <x-label for="cif" value="CIF*" class="text-azul text-base"/>
                                <x-input type="text" id="cif" class="w-full rounded-md" wire:model="cif" />
                                <x-input-error for="cif" />
                            </div>
                            <div class="col-span-2 sm:col-span-1 mb-4">
                                <x-label for="direccion_fac" value="Dirección de factura*" class="text-azul text-base"/>
                                <x-input type="text" id="direccion_fac" class="w-full rounded-md" wire:model="direccion_fac" />
                                <x-input-error for="direccion_fac" />
                            </div>
                            <div class="col-span-2 sm:col-span-1 mb-4">
                                <x-label for="cuenta" value="Cuenta Bancaria*" class="text-azul text-base"/>
                                <x-input type="text" id="cuenta" class="w-full rounded-md" wire:model="cuenta" />
                                <x-input-error for="cuenta" />
                            </div>
                        </div>
                    </form>
                </x-slot>

                <x-slot name="footer">
                    <spa class="px-4 py-2 text-center">Campos obligatorios (*)</spa>
                    <button type="button" wire:click="close" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                    <button type="submit" wire:click="save" class="bg-blue-500 text-white px-4 py-2 rounded">
                        {{ $editable ? 'Actualizar' : 'Crear' }}
                    </button>
                </x-slot>
            </div>
        </x-dialog-modal>
    @endif
</div>
