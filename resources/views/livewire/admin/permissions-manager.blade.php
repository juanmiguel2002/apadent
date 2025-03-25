<div class="mx-auto p-6 bg-white shadow-lg rounded-lg">
    <!-- Formulario para crear un nuevo permiso -->
    <div class="mb-6">
        <label for="permissionName" class="block text-sm font-medium text-gray-700">Nuevo Permiso</label>
            <form wire:submit.prevent="createPermission" >
                <div class="mt-2 flex items-center space-x-2">
                    <input type="text" wire:model="permissionName" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nombre del permiso">
                    <button type="submit" wire:submit="createPermission" class="px-6 py-2 bg-azul text-white rounded-lg shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Crear
                    </button>
                </div>
            </form>
        @error('permissionName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    </div>

    <!-- Lista de permisos -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-4">Permisos</h2>
        <ul class="space-y-3">
            @foreach ($permissions as $permission)
                <li class="flex items-center justify-between p-4 bg-gray-50 rounded-lg shadow-sm">
                    <span class="text-gray-800">{{ $permission->name }}</span>
                    <div class="space-x-3">
                        <button wire:click="deletePermission({{ $permission->id }})" class="text-red-600 hover:text-red-800 font-semibold">Eliminar</button>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
