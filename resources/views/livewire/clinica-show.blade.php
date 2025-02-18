<div>
    <div class="flex items-center justify-between mb-6">
        <!-- Contenedor del título -->
        <a href="javascript: history.go(-1)" class="flex items-center mr-4">
            <img class="w-6 mr-2" src="{{ asset('storage/recursos/icons/volver_naranja.png') }}" alt="Volver">
            <p class="text-lg font-semibold text-naranja">Atrás</p>
        </a>

        <div class="flex space-x-4">
            <!-- botón factura-->
            <button wire:click="openModal" class="flex items-center px-4 py-2 bg-azul text-white rounded-lg shadow-md hover:bg-teal-500 focus:outline-none focus:ring-2 focus:ring-green-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                <span>Añadir Factura</span>
            </button>
        </div>
    </div>
    <div class="my-3"></div>

    <div class="mx-auto p-6 bg-white shadow-md rounded-lg">
        <div class="flex space-x-6">
            <!-- Información del clinica -->
            <div class="w-2/3 space-y-4">
                <p class="text-lg font-semibold text-gray-800">Nombre: <span class="text-azul">{{ $clinica->name }}</span></p>
                <p class="text-lg font-semibold text-gray-800">
                    Responsable de la Clínica:
                    <span class="text-azul">
                        @foreach ($users as $user)
                            <span class="font-medium px-2">{{ $user->name }}</span>
                            <span class="inline-flex items-center bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                <span class="w-2 h-2 me-1 bg-green-500 rounded-full"></span>
                                @foreach ($user->roles as $role)
                                    {{ $role->name }}@if (!$loop->last), @endif
                                @endforeach
                            </span>
                        @endforeach
                    </span>
                </p>

                <p class="text-lg font-semibold text-gray-800">Email: <span class="text-azul">{{ $clinica->email }}</span></p>
                <p class="text-lg font-semibold text-gray-800">Teléfono: <span class="text-azul">{{ $clinica->telefono }}</span></p>
                <p class="text-lg font-semibold text-gray-800">Dirección: <span class="text-azul">{{ $clinica->direccion }}</span></p>

                <hr>
                <h2 class="text-xl font-semibold">Datos de facturación</h2>
                <p class="text-lg font-semibold text-gray-800">Razón Social: <span class="text-azul">{{ $clinica->name }}</span></p>
                <p class="text-lg font-semibold text-gray-800">CIF: <span class="text-azul">{{ $clinica->cif }}</span></p>
                <p class="text-lg font-semibold text-gray-800">Dirección: <span class="text-azul">{{ $clinica->direccion_fac }}</span></p>
                <p class="text-lg font-semibold text-gray-800">Nº Cuenta: <span class="text-azul">{{ $clinica->cuenta }}</span></p>
            </div>
            @can('factura_view')
                <div class="w-2/4 space-y-4 text-center">
                    <!-- Tabla facturas -->
                    <div class="mt-8">
                        <h2 class="text-lg font-semibold text-gray-800 mb-2">Facturas</h2>
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
                            <thead>
                                <tr class="bg-gray-100 border-b">
                                    <th class="px-4 py-2 text-center text-gray-600">Nombre</th>
                                    <th class="px-4 py-2 text-center text-gray-600">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($facturas as $factura)
                                    <tr>
                                        <td class="py-2">
                                            <a href="{{ route('facturas.view', ['ruta' => $factura]) }}" target="_black">{{ $factura->name }}</a>
                                        </td>
                                        <td class="py-2">
                                            <a href="{{ route('facturas.download', $factura) }}" target="_blank" class="text-azul">Descargar</a>
                                            {{-- @if (auth()->user()->hasRole('admin'))
                                                <a href="{{ route('eliminar.archivo', ['factura'=> $factura]) }}" onclick="event.preventDefault(); showConfirmModal({{ route('eliminar.archivo', ['factura'=> $factura]) }})" class="text-red-500 cursor-pointer">Eliminar</a>
                                            @endif --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $facturas->links() }}
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
    <div class="my-4"></div>

    @if ($modalOpen)
        <x-dialog-modal wire:model="modalOpen" maxWidth="lg">
            <x-slot name="title">
                <div class="flex justify-between items-center">
                    <h1 class="text-xl font-medium text-gray-800">Añadir Factura</h1>
                    <button wire:click='close' class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </x-slot>
            <x-slot name="content">
                <x-label for="file" class="block text-md text-azul capitalize" value="nombre" />
                <x-input wire:model="name" placeholder="factura-11-02-2000" type="text" class="block w-full px-3 py-2 mt-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-300 focus:ring-opacity-40"/>

                <x-label for="file" class="block text-md text-azul capitalize" value="Factura" />
                <x-input wire:model="factura" required type="file" class="block w-full px-3 py-2 mt-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-300 focus:ring-opacity-40" />
            </x-slot>
            <x-slot name="footer">
                <button type="button" wire:click="close" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                <button type="submit" wire:click="save" class="bg-azul text-white px-4 py-2 rounded">Añadir</button>
            </x-slot>
        </x-dialog-modal>
    @endif
</div>
