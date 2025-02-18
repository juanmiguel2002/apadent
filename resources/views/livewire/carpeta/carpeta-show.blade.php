<div>
    <div class="mb-6 mt-4">
        <div class="flex justify-end mt-2 gap-4">
            <a href="{{ route('admin.archivos') }}" class="bg-azul text-white px-4 py-2 rounded hover:bg-blue-600">
                Atrás
            </a>
            <button wire:click="showCreateModal" class="bg-azul text-white px-4 py-2 rounded hover:bg-blue-700">
                Nueva subcarpeta
            </button>
        </div>
    </div>
    <hr>
    <br>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($subcarpetas as $subcarpeta)
            <div class="bg-white shadow-md rounded-lg p-5 flex flex-col space-y-4">
                <!-- Ícono y Nombre -->
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 48 48">
                        <linearGradient id="folderGradient1" x1="24" x2="24" y1="6.708" y2="14.977" gradientUnits="userSpaceOnUse">
                            <stop offset="0" stop-color="#eba600"></stop>
                            <stop offset="1" stop-color="#c28200"></stop>
                        </linearGradient>
                        <path fill="url(#folderGradient1)" d="M24.414,10.414l-2.536-2.536C21.316,7.316,20.553,7,19.757,7L5,7C3.895,7,3,7.895,3,9l0,30c0,1.105,0.895,2,2,2l38,0c1.105,0,2-0.895,2-2V13c0-1.105-0.895-2-2-2l-17.172,0C25.298,11,24.789,10.789,24.414,10.414z"></path>
                        <linearGradient id="folderGradient2" x1="24" x2="24" y1="10.854" y2="40.983" gradientUnits="userSpaceOnUse">
                            <stop offset="0" stop-color="#ffd869"></stop>
                            <stop offset="1" stop-color="#fec52b"></stop>
                        </linearGradient>
                        <path fill="url(#folderGradient2)" d="M21.586,14.414l3.268-3.268C24.947,11.053,25.074,11,25.207,11H43c1.105,0,2,0.895,2,2v26c0,1.105-0.895,2-2,2H5c-1.105,0-2-0.895-2-2V15.5C3,15.224,3.224,15,3.5,15h16.672C20.702,15,21.211,14.789,21.586,14.414z"></path>
                    </svg>
                    <a href="{{ route('admin.archivos.view', $subcarpeta->id) }}" class="text-lg font-semibold text-gray-800 hover:text-blue-500 transition">
                        {{ basename($subcarpeta->nombre) }}
                    </a>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-between w-full">
                    <button wire:click="showEditModal('{{ $subcarpeta->id }}')"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-2.036a2.5 2.5 0 11-3.536-3.536L4 15v4h4l10.293-10.293z" />
                        </svg>
                        Editar
                    </button>

                    <button wire:click="delete('{{ basename($subcarpeta['name']) }}')"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 flex items-center transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Eliminar
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    @if ($archivos->isNotEmpty())
        <div class="max-w-7xl mx-auto x-4s m:px-6 lg:px-8 mt-6">
            <x-tabla>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="text-white">
                        <tr class="bg-azul">
                            <th class="p-3 text-center uppercase">ID</th>
                            <th class="p-3 text-center uppercase">Nombre</th>
                            <th class="p-3 text-center uppercase">Fecha</th>
                            <th class="p-3 text-center uppercase">Tipo</th>
                            <th class="p-3 text-center uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($archivos as $archivo)
                            <tr class="bg-gray-100 hover:bg-gray-200 transition">
                                <td class="px-2 py-4 text-center border-b">{{ $archivo->id }}</td>
                                <td class="px-6 py-4 text-center border-b">
                                    {!! \App\Helpers\FileHelper::getFileIcon($archivo->extension) !!}
                                    <span>{{ $archivo->name }}</span>
                                </td>
                                <td class="px-6 py-4 text-center border-b">{{ $archivo->created_at }}</td>
                                <td class="px-6 py-4 text-center border-b">{{ $archivo['extension'] }}</td>
                                <td class="px-6 py-4 text-center border-b">
                                    <a href="{{ $archivo['url'] }}" target="_blank" class="text-blue-600 hover:underline">Descargar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-tabla>
        </div>
    @endif

    @if ($facturas->isNotEmpty())
        <div class="max-w-7xl mx-auto x-4s m:px-6 lg:px-8 mt-6">
            <x-tabla>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="text-white">
                        <tr class="bg-azul">
                            <th class="p-3 text-center uppercase">ID</th>
                            <th class="p-3 text-center uppercase">Nombre</th>
                            <th class="p-3 text-center uppercase">Clínica</th>
                            <th class="p-3 text-center uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($facturas as $factura)
                            <tr class="bg-gray-100 hover:bg-gray-200 transition">
                                <td class="px-2 py-4 text-center border-b">{{ $factura->id }}</td>
                                <td class="px-6 py-4 text-center border-b">
                                    {!! \App\Helpers\FileHelper::getFileIcon($factura->extension) !!}
                                    <span>{{ $factura->name }}</span>
                                </td>
                                <td class="px-6 py-4 text-center border-b">{{ $factura->created_at }}</td>
                                <td class="px-6 py-4 text-center border-b">
                                    <a href="{{ $factura['url'] }}" target="_blank" class="text-blue-600 hover:underline">Descargar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </x-tabla>
        </div>
    @endif


    @if ($showModal)
        <x-dialog-modal maxWidth="lg">
            <div class="relative">
                <x-slot name="title">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold">
                            {{ $isEditing ? 'Editar Subcarpeta' : 'Nueva Subcarpeta' }}
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
                                <x-label value="Nombre*" class="text-azul text-base"/>
                                <x-input type="text" class="w-full rounded-md" wire:model="nombre" placeholder="Nombre" />
                                <x-input-error for="nombre" />
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
</div>
