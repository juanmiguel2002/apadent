<div>
    <!-- Crear Nueva Carpeta -->
    <div class="mb-6">
        {{-- <label for="newFolderName" class="block text-sm font-medium text-gray-700">Nueva Carpeta</label> --}}
        <div class="flex mt-2">
            <input type="text" wire:model="newFolderName"
                class="flex-grow border border-gray-300 rounded-l-md px-3 py-2"
                placeholder="Nombre de la carpeta"
            >
            <button wire:click="createFolder"
                class="bg-azul text-white px-4 py-2 rounded-r-md hover:bg-blue-700">
                Crear
            </button>
        </div>
    </div>

    <!-- Listado de Carpetas -->
    <div>
        <h2 class="text-xl font-semibold mb-3">Carpetas y archivos existentes</h2>
        @if ((Auth::user()->hasRole('admin') && $currentPath !== 'clinicas') ||
            (Auth::user()->hasRole('doctor_admin') && $currentPath !== 'clinicas/' . Auth::user()->clinicas->first()->name)
        )
            <button wire:click="navigateBack" class="mb-4 bg-azul text-white px-4 py-2 rounded">Atrás</button>
        @endif
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($directories as $directory)
                <div class="bg-white shadow rounded-md p-4 flex flex-col items-start">
                    <!-- Ícono y Nombre -->
                    <div class="flex items-center w-full">
                        <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="80" height="80" viewBox="0 0 48 48">
                            <linearGradient id="WQEfvoQAcpQgQgyjQQ4Hqa_dINnkNb1FBl4_gr1" x1="24" x2="24" y1="6.708" y2="14.977" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#eba600"></stop><stop offset="1" stop-color="#c28200"></stop></linearGradient>
                            <path fill="url(#WQEfvoQAcpQgQgyjQQ4Hqa_dINnkNb1FBl4_gr1)" d="M24.414,10.414l-2.536-2.536C21.316,7.316,20.553,7,19.757,7L5,7C3.895,7,3,7.895,3,9l0,30	c0,1.105,0.895,2,2,2l38,0c1.105,0,2-0.895,2-2V13c0-1.105-0.895-2-2-2l-17.172,0C25.298,11,24.789,10.789,24.414,10.414z"></path>
                            <linearGradient id="WQEfvoQAcpQgQgyjQQ4Hqb_dINnkNb1FBl4_gr2" x1="24" x2="24" y1="10.854" y2="40.983" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#ffd869"></stop><stop offset="1" stop-color="#fec52b"></stop></linearGradient>
                            <path fill="url(#WQEfvoQAcpQgQgyjQQ4Hqb_dINnkNb1FBl4_gr2)" d="M21.586,14.414l3.268-3.268C24.947,11.053,25.074,11,25.207,11H43c1.105,0,2,0.895,2,2v26	c0,1.105-0.895,2-2,2H5c-1.105,0-2-0.895-2-2V15.5C3,15.224,3.224,15,3.5,15h16.672C20.702,15,21.211,14.789,21.586,14.414z"></path>
                        </svg>
                        <button wire:click="navigateTo('{{ basename($directory['name']) }}')" class="ml-2 text-lg font-medium text-gray-700">{{ basename($directory['name']) }}</button>
                    </div>

                    <!-- Archivos y Fecha -->
                    <div class="mt-3 w-full text-sm text-gray-600">
                        @if ($directory['fileCount'])
                            <p class="text-sm text-gray-600 mb-2">
                                <strong>Archivos:</strong> {{ $directory['fileCount'] }}
                            </p>
                        @else
                            <p class="text-sm text-gray-600 mb-2">
                                <strong>No tiene archivos.</strong>
                            </p>
                        @endif
                        <p class="text-sm text-gray-600">
                            <strong>Modificado:</strong> {{ $directory['lastModified'] }}
                        </p>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="mt-4 flex justify-between w-full">
                        <button
                            wire:click="editFolder('{{ basename($directory['name']) }}')"
                            class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-2.036a2.5 2.5 0 11-3.536-3.536L4 15v4h4l10.293-10.293z" />
                            </svg>
                            Editar
                        </button>
                        <button
                            wire:click="delete('{{ basename($directory['name']) }}')"
                            class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Eliminar
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">
            {{-- <h3 class="font-semibold text-lg mb-2">Archivos:</h3> --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
                @foreach ($files as $file)
                    <div class="bg-white p-4 rounded shadow">
                        <h3 class="text-sm font-medium text-gray-800">{{ $file['name'] }}</h3>
                        <p class="text-sm text-gray-500">Modificado: {{ $file['lastModified'] }}</p>
                        <p class="text-sm text-gray-500">Tipo: {{ $file['extension'] }}</p>
                        <div class="mt-4">
                            <a href="{{ $file['url'] }}" target="_blank" class="text-blue-600 hover:underline">Descargar</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
