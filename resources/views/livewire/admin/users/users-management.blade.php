<div class="container mx-auto px-4 sm:px-8 max-w-[100%]">
    <div class="py-8">
        <div class="flex flex-row justify-between w-full mb-1 sm:mb-0">
            <div>Mostrar
                <select class="px-5 py-1 text-base text-gray-700 placeholder-gray-400 bg-white border border-transparent border-gray-400 rounded-lg shadow-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent "
                    wire:model="perPage">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                </select>
                Registros
            </div>

            <div class="text-end">
                <form class="flex flex-col md:flex-row md:space-x-3 md:space-y-0 space-y-3 w-full">
                    <div class="relative flex-1">
                        <input type="text"
                            class="w-full px-4 py-2 text-base text-gray-700 placeholder-gray-400 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-purple-600"
                            placeholder="Buscar" wire:model.live="search" />
                    </div>

                    <div class="relative flex-1">
                        <x-users.component-input-select placeholder="Seleccione" :options="$roles" name="user_role" label="" />
                    </div>

                    @can('usuario_create')
                        <div class="flex-shrink-0" >
                            <button type="button" wire:click='showCreateModal'
                                class="px-4 py-2 text-base font-semibold text-white bg-azul rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                <i class="fas fa-plus mr-2"></i> Nuevo
                            </button>
                        </div>
                    @endcan
                </form>
            </div>
        </div>
    </div>

    <x-tabla>
        <table class="min-w-full">
            <thead class="text-white">
                <tr class="bg-azul">
                    <th scope="col"
                        class="px-5 py-3 text-sm font-normal text-left text-white uppercase bg-azul border-b border-gray-200">
                        ID
                        <button wire:click="sortable('id')">
                            <span class="fa fa{{ $camp === 'id' ? $icon : '-sort' }}"></span>
                        </button>
                    </th>
                    <th scope="col"
                        class="px-5 py-3 text-sm font-normal text-left text-white uppercase bg-azul border-b border-gray-200 ">
                        Nombre
                        <button wire:click="sortable('name')">
                            <span class="fa fa{{ $camp === 'name' ? $icon : '-sort' }}"></span>
                        </button>
                    </th>
                    <th scope="col"
                        class="px-5 py-3 text-sm font-normal text-left text-white uppercase bg-azul border-b border-gray-200">
                        Correo electrónico
                        <button wire:click="sortable('email')">
                            <span class="fa fa{{ $camp === 'email' ? $icon : '-sort' }}"></span>
                        </button>
                    </th>
                    <th scope="col"
                        class="px-5 py-3 text-sm font-normal text-left text-white uppercase bg-azul border-b border-gray-200">
                        Rol
                    </th>
                    <th scope="col"
                        class="px-5 py-3 text-sm font-normal text-left text-white uppercase bg-azul border-b border-gray-200">
                        Clínica
                    </th>
                    <th scope="col"
                        class="px-5 py-3 text-sm font-normal text-left text-white uppercase bg-azul border-b border-gray-200">
                        Creado
                        <button wire:click="sortable('created_at')">
                            <span class="fa fa{{ $camp === 'created_at' ? $icon : '-sort' }}"></span>
                        </button>
                    </th>
                    <th scope="col"
                        class="px-5 py-3 text-sm font-normal text-center text-white uppercase bg-azul border-b border-gray-200"
                        >Acciones
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="ml-3">
                                    <p class="text-gray-900 whitespace-no-wrap">
                                        {{ $user->id }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            <p class="text-gray-900 whitespace-no-wrap">
                                {{ $user->name }}
                            </p>
                        </td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            <p class="text-gray-900 whitespace-no-wrap">
                                {{ $user->email }}
                            </p>
                        </td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            <p class="text-gray-900 whitespace-no-wrap">
                                {{ $user->roles()->first()->name ?? 'Sin rol' }}
                            </p>
                        </td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            <p class="text-gray-900 whitespace-no-wrap">
                                {{$user->clinicas()->first()->name ?? 'No tiene clínica asignada'}}
                            </p>
                        </td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            <p class="text-gray-900 whitespace-no-wrap">
                                {{ $user->created_at->format('d-m-Y') }}
                            </p>
                        </td>
                        <td class="px-5 py-5 text-sm bg-white border-b border-gray-200">
                            <div class="flex items-center justify-center gap-x-2">
                                @can('usuario_update')
                                    <a href="#" wire:click.prevent="showCreateModal({{ $user->id }})"
                                        class="flex items-center justify-center px-4 py-2 text-base font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                                        <i class="fas fa-edit mr-2"></i> Editar
                                    </a>
                                @endcan
                                @can('rol_create')
                                    <a href="#" wire:click="verPermisos({{ $user }}, {{$user->roles()->first()->id}})"
                                        class="flex items-center justify-center px-4 py-2 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:text-sm">
                                        <i class="fas fa-plus mr-2"></i> Permisos
                                    </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 mt-4">No se encontró ningún registro</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-tabla>

    <div class="flex-col items-center px-5 py-5 bg-white xs:flex-row xs:justify-between">
        <div class="items-center ">
            {!! $users->links() !!}
        </div>
    </div>

    @if ($showModal)
        <x-dialog-modal maxWidth="lg" x-data="{ showModal: @entangle('showModal') }">
            <div class="relative">
                <x-slot name="title">
                    <div class="flex justify-between items-center">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-azul" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold">
                            {{ $isEditing ? 'Editar Usuario' : 'Añadir Usuario' }}
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
                            <div class="col-span-2 mb-4 sm:col-span-1">
                                <x-label for="name" value="Nombre*" class="text-azul text-base"/>
                                <x-input type="text" id="name" class="w-full rounded-md" wire:model.defer="name" placeholder="Nombre" />
                                <x-input-error for="name" />
                            </div>

                            <div class="col-span-2 mb-4 sm:col-span-1">
                                <x-label for="colegiado" value="Núm Colegiado*" class="text-azul text-base"/>
                                <x-input type="text" id="colegiado" class="w-full rounded-md" wire:model.defer="colegiado" placeholder="Núm Colegiado" />
                                <x-input-error for="colegiado" />
                            </div>

                            <div class="col-span-2 mb-4">
                                <x-label for="email" value="Email*" class="text-azul text-base"/>
                                <x-input type="email" id="email" class="w-full rounded-md" wire:model.defer="email" placeholder="Email" />
                                <x-input-error for="email" />
                            </div>

                            @if (!$isEditing)
                                <div class="col-span-2 mb-4">
                                    <x-label for="password" value="Contraseña*" class="text-azul text-base"/>
                                    <x-input id="password" class="w-full rounded-md" wire:model.defer="password" type="password" placeholder="Contraseña" />
                                    <x-input-error for="password" />
                                </div>

                                <div class="col-span-2 mb-4">
                                    <x-label for="password_confirmation" value="Confirma Contraseña*" class="text-azul text-base"/>
                                    <x-input id="password_confirmation" class="w-full rounded-md" wire:model.defer="password_confirmation" type="password" placeholder="Confirmar contraseña" />
                                    <x-input-error for="password_confirmation" />
                                </div>
                            @endif

                            @if ($clinicas)
                                <div class="col-span-2 mb-4">
                                    <x-label for="clinica_id" value="Asigna una Clínica*" class="text-azul text-base"/>
                                    <select id="clinica_id" wire:model.defer="selectedClinica" class="form-input block w-full rounded-md border border-[rgb(224,224,224)]">
                                        <option value="">Selecciona una Clínica</option>
                                        @foreach($clinicas as $clinica)
                                            <option value="{{ $clinica->id }}">{{ $clinica->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error for="selectedClinica" />
                                </div>
                            @endif

                            <div class="col-span-2 mb-4">
                                <x-label for="role_id" value="Asigna un Role*" class="text-azul text-base"/>
                                <select id="role_id" wire:model.defer="selectedRole" class="form-input block w-full rounded-md border border-[rgb(224,224,224)]">
                                    <option value="">Seleccione un Role</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="selectedRole" />
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

    @if ($showPermisos)
        <x-dialog-modal maxWidth="lg" x-data="{ showPermisos: @entangle('showPermisos') }">
            <div class="relative">
                <x-slot name="title">
                    <div class="flex justify-between items-center">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-azul" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold">Permisos registrados para {{ $user->name }}</h2>
                        <button wire:click='close' class="text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </x-slot>

                <x-slot name="content">
                    <form wire:submit.prevent="save">
                        <div class="space-y-4">
                            @foreach ($permissions as $permission)
                                <div>
                                    <label for="permission-{{ $permission->id }}" class="inline-flex items-center">
                                        <!-- Checkbox que se marca si el permiso está asignado al usuario -->
                                        <input type="checkbox"
                                               wire:model="selectedPermissions.{{ $permission->id }}.check"
                                               value="{{ $permission->id }}">
                                        <span class="ml-2">{{ $permission->name }}</span>
                                    </label>
                                </div>
                            @endforeach
                    </form>
                </x-slot>

                <x-slot name="footer">
                    <button type="button" wire:click="close" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                    <button type="submit" wire:click="savePermissions" class="bg-blue-500 text-white px-4 py-2 rounded">
                        {{ 'Modificar' }}
                    </button>
                </x-slot>
            </div>
        </x-dialog-modal>
    @endif
</div>
