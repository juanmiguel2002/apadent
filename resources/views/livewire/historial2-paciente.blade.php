<div>
    <!-- Mostrar Tratamiento Seleccionado -->
    @if ($tratId)
        <h2 class="px-2 mb-4 font-semibold text-lg text-gray-800">Tratamiento seleccionado:
            {{ $tratamiento->tratamiento->name }} - {{ $tratamiento->tratamiento->descripcion }}
        </h2>
    @else
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Tratamientos</h2>
            <select wire:model="tratamientoId" wire:change="loadFases" class="w-full p-3 border rounded-md">
                <option value="">Seleccione un tratamiento</option>
                @foreach($tratamiento as $trat)
                    <option value="{{ $trat->tratamiento->id }}">{{ $trat->tratamiento->name }} - {{ $trat->tratamiento->descripcion }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <!-- Acordeón de Fases y Etapas -->
    @if($etapas)
        <div class="space-y-4">
            @foreach($etapas as $etapa)
                <div class="bg-white shadow-md rounded-md">
                    <button wire:click="toggleAcordeon('{{ $etapa->fase->name }}')" class="w-full text-left px-4 py-3 bg-azul text-white font-semibold rounded-t-md focus:outline-none" data-accordion-target="#accordion-flush-body-1" aria-expanded="true" aria-controls="accordion-flush-body-1">
                        {{$etapa->fase->name}}
                    </button>

                    @if($mostrarMenu[$etapa->fase->name] ?? false)
                        <div class="px-4 py-2">
                            <table class="min-w-full bg-gray-50">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 bg-azul">ID</th>
                                        <th class="px-4 py-2 bg-azul">Nº de Fase</th>
                                        <th class="px-4 py-2 bg-azul">Mensaje</th>
                                        <th class="px-4 py-2 bg-azul">Estado</th>
                                        <th class="px-4 py-2 bg-azul">Revisión</th>
                                        <th class="px-4 py-2 bg-azul">Archivos</th>
                                        <th class="px-4 py-2 bg-azul">Imágenes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($etapas as $etapa)
                                            <tr class="hover:bg-gray-100 transition duration-300">
                                                <td class="px-4 py-2 text-center">{{ $etapa->id }}</td>
                                                <td class="px-4 py-2">{{ $etapa->name }}</td>
                                                <td class="px-4 py-2">
                                                    @foreach ($etapa->mensajes as $mensaje)
                                                        <div class="text-left mb-2">
                                                            <p class="text-blue-500 font-light">{{ $mensaje->mensaje }}</p>
                                                            <p class="text-xs font-normal text-gray-500">{{ $mensaje->user->name }}</p>
                                                            <p class="text-xs font-light text-gray-400">{{ $mensaje->created_at->format('d-m-Y H:i') }}</p>
                                                        </div>
                                                    @endforeach
                                                </td>
                                                <td class="px-4 py-2 text-center">
                                                    <span class="px-3 py-1 rounded-full text-white bg-blue-600">
                                                        {{ $etapa->status }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 text-center">
                                                    @if($etapa->revision)
                                                        <span class="text-sm font-semibold">{{ \Carbon\Carbon::parse($etapa->revision)->format('d-m-Y') }}</span>
                                                    @else
                                                        <button wire:click="abrirModalRevision({{ $etapa->id }})" class="mt-2 px-4 py-1 bg-indigo-600 text-white rounded-md">
                                                            Asignar Fecha
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            @endforeach

        </div>
    @endif
</div>
