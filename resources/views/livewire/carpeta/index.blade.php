<div>
    <div class="mb-6 mt-4">
        {{-- <div class="flex justify-end mt-2">
            <button wire:click="showCreateModal" class="bg-azul text-white px-4 py-2 rounded hover:bg-blue-700">
                Nueva carpeta
            </button>
        </div> --}}
        @include('components.alert-message')
    </div>
    <hr>
    <br>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($carpetas as $carpeta)
            <div class="bg-white shadow-md rounded-md p-4 flex flex-col items-start">
                <!-- Ícono y Nombre -->
                <div class="flex items-center w-full">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="80" height="80" viewBox="0 0 48 48">
                        <linearGradient id="WQEfvoQAcpQgQgyjQQ4Hqa_dINnkNb1FBl4_gr1" x1="24" x2="24" y1="6.708" y2="14.977" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#eba600"></stop><stop offset="1" stop-color="#c28200"></stop></linearGradient>
                        <path fill="url(#WQEfvoQAcpQgQgyjQQ4Hqa_dINnkNb1FBl4_gr1)" d="M24.414,10.414l-2.536-2.536C21.316,7.316,20.553,7,19.757,7L5,7C3.895,7,3,7.895,3,9l0,30	c0,1.105,0.895,2,2,2l38,0c1.105,0,2-0.895,2-2V13c0-1.105-0.895-2-2-2l-17.172,0C25.298,11,24.789,10.789,24.414,10.414z"></path>
                        <linearGradient id="WQEfvoQAcpQgQgyjQQ4Hqb_dINnkNb1FBl4_gr2" x1="24" x2="24" y1="10.854" y2="40.983" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#ffd869"></stop><stop offset="1" stop-color="#fec52b"></stop></linearGradient>
                        <path fill="url(#WQEfvoQAcpQgQgyjQQ4Hqb_dINnkNb1FBl4_gr2)" d="M21.586,14.414l3.268-3.268C24.947,11.053,25.074,11,25.207,11H43c1.105,0,2,0.895,2,2v26	c0,1.105-0.895,2-2,2H5c-1.105,0-2-0.895-2-2V15.5C3,15.224,3.224,15,3.5,15h16.672C20.702,15,21.211,14.789,21.586,14.414z"></path>
                    </svg>
                    <a href="{{ route('admin.archivos.view', $carpeta->id) }}" class="ml-2 text-lg font-medium text-gray-700 hover:text-blue-600">{{ $carpeta->nombre }}</a>
                </div>

                <!-- Archivos y Fecha -->
                <div class="mt-3 w-full text-sm text-gray-600">
                    <p class="text-sm text-gray-600">
                        <strong>Modificado:</strong> {{ $carpeta->created_at }}
                    </p>
                </div>

                <!-- Botones de Acción -->
                <div class="mt-4 flex justify-between w-full">
                    <button
                        wire:click="showEditModal('{{ $carpeta->id }}')"
                        class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-2.036a2.5 2.5 0 11-3.536-3.536L4 15v4h4l10.293-10.293z" />
                        </svg>
                        Editar
                    </button>
                    <form action="{{ route('admin.archivos.delete', $carpeta->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta clínica?')">
                        @csrf
                        <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Eliminar
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-white shadow-md rounded-lg p-5 flex flex-col">
                <!-- Ícono y Nombre -->
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="80" height="80" viewBox="0 0 48 48">
                        <linearGradient id="WQEfvoQAcpQgQgyjQQ4Hqa_dINnkNb1FBl4_gr1" x1="24" x2="24" y1="6.708" y2="14.977" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#eba600"></stop><stop offset="1" stop-color="#c28200"></stop></linearGradient>
                        <path fill="url(#WQEfvoQAcpQgQgyjQQ4Hqa_dINnkNb1FBl4_gr1)" d="M24.414,10.414l-2.536-2.536C21.316,7.316,20.553,7,19.757,7L5,7C3.895,7,3,7.895,3,9l0,30	c0,1.105,0.895,2,2,2l38,0c1.105,0,2-0.895,2-2V13c0-1.105-0.895-2-2-2l-17.172,0C25.298,11,24.789,10.789,24.414,10.414z"></path>
                        <linearGradient id="WQEfvoQAcpQgQgyjQQ4Hqb_dINnkNb1FBl4_gr2" x1="24" x2="24" y1="10.854" y2="40.983" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#ffd869"></stop><stop offset="1" stop-color="#fec52b"></stop></linearGradient>
                        <path fill="url(#WQEfvoQAcpQgQgyjQQ4Hqb_dINnkNb1FBl4_gr2)" d="M21.586,14.414l3.268-3.268C24.947,11.053,25.074,11,25.207,11H43c1.105,0,2,0.895,2,2v26	c0,1.105-0.895,2-2,2H5c-1.105,0-2-0.895-2-2V15.5C3,15.224,3.224,15,3.5,15h16.672C20.702,15,21.211,14.789,21.586,14.414z"></path>
                    </svg>
                    No hay archivos
                </div>
            </div>
        @endforelse
    </div>

    @if ($showModal)
        <x-dialog-modal maxWidth="lg">
            <div class="relative">
                <x-slot name="title">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-bold">
                            {{ $isEditing ? 'Editar carpeta' : 'Nueva carpeta' }}
                        </h2>
                        <button wire:click='close' class="text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </x-slot>

                <x-slot name="content">
                    @include('components.alert-message')
                    <form wire:submit.prevent="save">
                        <div class="grid grid-cols-1 gap-4">
                            <div class="col-span-2 mb-4 sm:col-span-1">
                                <x-label value="Nombre*" class="text-azul text-base"/>
                                <x-input type="text" class="w-full rounded-md" wire:model="newName" placeholder="Nombre" />
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
