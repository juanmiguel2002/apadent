    <div>
    @include('components.alert-message')
    @role('admin')
        <x-input-select-clinicas  :options="$clinicas" label="Selecciona una clínica" name="clinicaSelected"/>
    @endrole
    <div class="px-4 py-4 flex justify-between items-center">
        <div class="flex justify-start items-center">
            <span class="uppercase text-base text-azul font-light">Ordenar por: </span>
            <select wire:model.live="ordenar"
                class="px-4 py-2 mt-2 ml-5 text-azul placeholder-gray-400 bg-white border border-azul rounded-none focus:border-none focus:outline-none focus:ring-none focus:ring-none focus:ring-opacity-40">
                <option value="name">A-Z</option>
                <option value="recientes">Recientes</option>
                <option value="">Código</option>
            </select>
        </div>
        <div>
            Mostrar
            <select class="px-5 py-1 text-base text-gray-700 placeholder-gray-400 bg-white border border-transparent border-gray-400 rounded-lg shadow-sm appearance-none focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent "
                wire:model.live="perPage">
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="150">150</option>
            </select>
            Registros
        </div>
        <div class="flex justify-end items-center space-x-4">
            @can('paciente_create')
                <button wire:click="showCreateModal"
                        class="bg-azul hover:bg-azul-dark text-white font-semibold px-4 py-2 rounded shadow">
                    Crear Paciente
                </button>
            @endcan

            <div class="flex items-center">
                <x-input class="text-azul" type="text"
                    wire:model.live="search"
                    placeholder="Buscar paciente.." />
            </div>

            @can('paciente_update')
                <button wire:click="toggleVerInactivos"
                        class="px-4 py-2 rounded shadow font-semibold text-white
                            {{ $activo ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600' }}">
                    {{ $activo ? 'Ver Activos' : 'Ver Inactivos' }}
                </button>
            @endcan
        </div>
    </div>

    <x-tabla>
        <table class="min-w-full divide-y table-fixed">
            <thead class="text-white">
                <tr class="bg-azul">
                    <th class="p-3 text-center">Cód Paciente</th>
                    <th class="p-3 text-center">Nombre y Apellido</th>
                    @if (auth()->user()->hasRole('admin'))
                        <th class="p-3 text-center">Clínica</th>
                    @endif
                    <th class="p-3 text-center">Teléfono</th>
                    <th class="p-3 text-center">Tratamiento</th>
                    <th class="p-3 text-center">Status</th>
                    <th class="p-3 text-center">Nº de Fase</th>
                    <th class="p-3 text-center">Acción</th>
                </tr>
            </thead>
            @if ($pacientes->count())
                <tbody class="bg-gray-200 ">
                    @foreach ($pacientes as $paciente)
                        <tr>
                            <td class="text-center px-4 py-2">{{ $paciente->num_paciente }}</td>
                            <td class="text-center px-4 py-2 cursor-pointer">
                                <a href="{{ route('pacientes-show', $paciente->id) }}">
                                    {{ $paciente->name . " " . $paciente->apellidos }}
                                </a>
                            </td>
                            @if (auth()->user()->hasRole('admin'))
                                <td class="text-center px-4 py-2">
                                    {{ $paciente->clinicas->name ?? 'Sin clínica' }}
                                </td>
                            @endif
                            <td class="text-center px-4 py-2">{{ $paciente->telefono }}</td>

                            {{-- Tratamiento --}}
                            @php
                                $tratamiento = $paciente->tratamientos->first();
                            @endphp

                            <td class="text-center px-4 py-2">{{ $tratamiento->name ?? "Sin tratamiento"}} - {{ $tratamiento->descripcion }}</td>
                            {{-- Obtener la última fase y etapa del tratamiento más reciente --}}
                            @php
                                // $fase = optional($tratamiento)->fases->first();
                                $etapa = $tratamiento->etapas->where('paciente_id', $paciente->id)->last();
                            @endphp

                            @foreach ($statuses as $status => $color)
                                @if ($etapa->status == $status)
                                    <td class="p-3 text-center flex justify-center items-center mt-2">
                                        @if (auth()->user()->hasRole('admin') || ($status !== 'Finalizado' && auth()->user()))
                                            {{-- Si es admin o usuario normal (y el estado no es "Finalizado"), puede cambiarlo --}}
                                            <button
                                                wire:click="toggleMenu({{ $paciente->id }})"
                                                class="flex items-center justify-center px-6 text-white {{ $color }} font-medium rounded-xl">
                                                <span>{{ $status }}</span>
                                            </button>
                                            <img class="ml-2 w-3" src="{{ asset('storage/recursos/icons/flecha_abajo.png') }}" alt="flecha_abajo">

                                            {{-- Menú desplegable con opciones de cambio de estado --}}
                                            @if ($menuVisible === $paciente->id)
                                                <div class="ml-4 mt-2 space-y-1">
                                                    @foreach (['En proceso' => 'bg-green-600','Pausado' => 'bg-blue-600','Finalizado' => 'bg-red-600', 'Set Up' => 'bg-yellow-600'] as $optionStatus => $optionColor)
                                                        <div class="cursor-pointer text-white {{ $optionColor }} py-1 px-2 rounded-lg hover:bg-opacity-75"
                                                            wire:click="estado({{ $etapa->id }},'{{ $optionStatus }}')">
                                                            {{ $optionStatus }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @else
                                            {{-- Si el estado es "Finalizado" y no eres admin, solo lo ves sin opción de cambiarlo --}}
                                            <span class="flex items-center justify-center px-6 text-white {{ $color }} font-medium rounded-xl">
                                                <span>{{ $status }}</span>
                                            </span>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                            <td class="text-center px-4 py-2">Fase 1 <br> {{ $etapa->name }}</td>

                            <td class="text-center border px-4 py-2">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('paciente-historial', $paciente->id) }}">
                                        <img src="{{ asset('storage/recursos/icons/ojo_azul.png') }}" class="w-5 cursor-pointer">
                                    </a>
                                    @role('admin')
                                        <form action="{{ route('admin.pacientes.delete', $paciente->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este paciente?')">
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
                        @if ($pacientes->count() > 0)
                            @if ($pacientes->where('activo', "<>", 0))
                                No existe ningún paciente activo.
                            @else
                                No existe ningún paciente desactivado.
                            @endif
                        @else
                            No existe ningún paciente.
                        @endif
                    </td>
                </tr>
            @endif
        </table>
        @if ($pacientes->hasPages())
            {{ $pacientes->links('vendor.pagination.paginacion') }}
        @endif
    </x-tabla>

    {{-- Añadir paciente --}}
    @can('paciente_create')
        @if($showModal)
            <x-dialog-modal maxWidth="xl" wire:model="showModal">
                <div class="relative">
                    <x-slot name="title">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold">
                                {{ 'Añadir Paciente' }}
                            </h2>
                            <button wire:click='close' class="text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </x-slot>

                    <x-slot name="content">
                        <form wire:submit.prevent="save" enctype="multipart/form-data">
                            @csrf
                            <div class="grid grid-cols-2 gap-4">
                                @role('admin')
                                    <div class="col-span-2 mb-4">
                                        <x-label for="clinica_id" value="Clínica*" class="text-azul text-base"/>
                                        <select name="clinica_id" wire:model="clinica_id" class="form-input block w-full rounded-md border border-[rgb(224,224,224)]">
                                            <option value="">Seleccione una Clínica</option>
                                            @foreach($clinicas as $clinica)
                                                <option value="{{ $clinica->id }}">{{ $clinica->name }}</option>
                                            @endforeach
                                        </select>
                                        <x-input-error for="clinica_id" />
                                    </div>
                                @endrole
                                <div class="col-span-2 mb-4">
                                    <x-label for="num_paciente" value="Número Paciente*" class="text-azul text-base"/>
                                    <x-input type="text" id="num_paciente" class="w-full rounded-md" wire:model="num_paciente" placeholder="2002"/>
                                    <x-input-error for="num_paciente" />
                                </div>

                                <div class="col-span-2 sm:col-span-1 mb-4">
                                    <x-label for="name" value="Nombre*" class="text-azul text-base"/>
                                    <x-input type="text" id="name" class="w-full rounded-md" wire:model.defer="name" placeholder="Nombre" />
                                    <x-input-error for="name" />
                                </div>
                                <div class="col-span-2 sm:col-span-1 mb-4">
                                    <x-label for="name" value="Apellidos*" class="text-azul text-base"/>
                                    <x-input type="text" id="name" class="w-full rounded-md" wire:model.defer="apellidos" placeholder="Apellidos" />
                                    <x-input-error for="apellidos" />
                                </div>

                                <div class="col-span-2 sm:col-span-1 mb-4">
                                    <x-label for="email" value="Email*" class="text-azul text-base"/>
                                    <x-input type="email" id="email" class="w-full rounded-md" wire:model="email" placeholder="strat@gmail.com" />
                                    <x-input-error for="email" />
                                </div>

                                <div class="col-span-2 sm:col-span-1 mb-4">
                                    <x-label for="fecha_nacimiento" value="Fecha Nacimiento*" class="text-azul text-base"/>
                                    <x-input type="date" id="fecha_nacimiento" class="w-full rounded-md" wire:model="fecha_nacimiento" />
                                    <x-input-error for="fecha_nacimiento" />
                                </div>
                                <div class="col-span-2 sm:col-span-1 mb-4">
                                    <x-label for="telefono" value="Teléfono*" class="text-azul text-base"/>
                                    <x-input type="text" id="telefono" class="w-full rounded-md" wire:model="telefono" placeholder="978456123"/>
                                    <x-input-error for="telefono" />
                                </div>

                                <div class="col-span-2 sm:col-span-1 mb-4">
                                    <x-label for="tratamiento_id" value="Tratamiento*" class="text-azul text-base"/>
                                    <select name="tratamiento_id" wire:model="selectedTratamiento" class="form-input block w-full rounded-md border border-[rgb(224,224,224)]">
                                        <option value="">Seleccione un Tratamiento</option>
                                        @foreach($tratamientos as $tratamiento)
                                            <option value="{{ $tratamiento->id }}">{{ $tratamiento->name }} - {{ $tratamiento->descripcion}}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error for="selectedTratamiento" />
                                </div>

                                <div class="col-span-2 mb-4">
                                    <x-label for="observacion" value="Observaciones" class="text-azul text-md"/>
                                    <textarea wire:model="observacion" class="w-full rounded-md border border-[rgb(224,224,224)] resize-none" rows="4" ></textarea>
                                    <x-input-error for="observacion" />
                                </div>
                                <div class="col-span-2 mb-3">
                                    <x-label for="img_paciente" class="block text-md text-azul capitalize">Foto paciente</x-label>
                                    <x-input wire:model="img_paciente" type="file" class="block w-full px-3 py-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-300 focus:ring-opacity-40" />
                                    <x-input-error for="img_paciente" />
                                </div>
                                <div class="col-span-2 mb-3">
                                    <x-label for="imagenes" class="block text-md text-azul capitalize">Fotografías</x-label>
                                    <x-input type="file" wire:model="imagenes" multiple class="block w-full px-3 py-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-300 focus:ring-opacity-40" />
                                    <x-input-error for="imagenes.*" />
                                </div>

                                <div class="col-span-2 mb-4">
                                    <x-label for="cbct" class="block text-md text-azul capitalize">Archivos CBCT <i>(comprimidos .zip)</i></x-label>
                                    <div id="file-uploader"></div>
                                </div>

                                <div class="col-span-2 mb-3">
                                    <x-label for="rayos" class="block text-md text-azul capitalize">Archivos RX</x-label>
                                    <x-input type="file" wire:model="rayos" multiple class="block w-full px-3 py-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-300 focus:ring-opacity-40" />
                                </div>

                                <div class="col-span-2 mb-4">
                                    <x-label for="obser_cbct" value="Observaciones Cbct/Imágenes" class="text-azul text-base"/>
                                    <textarea wire:model="obser_cbct" class="w-full rounded-md border border-[rgb(224,224,224)] resize-none" rows="4" placeholder="Definir medididas, intraorales o físicas"></textarea>
                                    <x-input-error for="obser_cbct" />
                                </div>
                            </div>
                        </form>
                    </x-slot>

                    <x-slot name="footer">
                        <span class="px-4 py-2 text-center">Campos obligatorios (*)</span>
                        <button type="button" wire:click="close" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Cancelar</button>
                        <button type="submit" wire:click="save" class="bg-blue-500 text-white px-4 py-2 rounded">
                            {{ 'Crear' }}
                        </button>
                    </x-slot>
                </div>
            </x-dialog-modal>
        @endif
    @endcan
    @push('scripts')
        {{-- <script type="text/javascript">
            // document.addEventListener("DOMContentLoaded", function () {
                let dropZone = document.getElementById("dropZone");
                let fileInput = document.getElementById("fileInput");
                let progressBar = document.getElementById("uploadProgress");
                let statusText = document.getElementById("uploadStatus");
                // let submitButton = document.getElementById("submitButton");

                let resumable = new Resumable({
                    target: "{{ route('upload') }}",
                    query: { _token: "{{ csrf_token() }}" },
                    fileType: ["zip"],
                    chunkSize: 5 * 1024 * 1024,
                    simultaneousUploads: 3,
                    testChunks: true,
                    throttleProgressCallbacks: 1,
                    forceChunkSize: true
                });

                resumable.assignBrowse(fileInput);
                resumable.assignDrop(dropZone);

                // ⬇️ Ahora el clic en el dropZone abre el selector de archivos ⬇️
                dropZone.addEventListener("click", function () {
                    fileInput.click();
                });

                fileInput.addEventListener("change", function (event) {
                    let files = event.target.files;
                    if (files.length > 0) {
                        resumable.addFile(files[0]);
                    }
                });

                resumable.on("fileAdded", function (file) {
                    progressBar.classList.remove("hidden");
                    statusText.textContent = "Preparando subida...";
                    submitButton.disabled = true;
                    resumable.upload();
                });

                resumable.on("fileProgress", function (file) {
                    let progress = Math.floor(file.progress() * 100);
                    progressBar.value = progress;
                    statusText.textContent = `Subiendo... ${progress}%`;
                });

                resumable.on("fileSuccess", function (file, response) {
                    statusText.textContent = "Subida completada.";
                    progressBar.classList.add("hidden");

                    Livewire.emit('actualizarEstadoCBCT', JSON.parse(response).filename);

                    submitButton.disabled = false;
                });

                resumable.on("fileError", function (file, message) {
                    statusText.textContent = "Error en la subida. Verifica tu conexión.";
                    progressBar.classList.add("hidden");
                    submitButton.disabled = true;
                });

                resumable.on("complete", function () {
                    statusText.textContent = "Archivo subido correctamente.";
                });
            // });
        </script> --}}
        <script type="text/javascript">
            var uploader = new plupload.Uploader({
                browse_button: 'file-uploader',  // ID del botón para seleccionar archivos
                url: '/upload',  // URL de tu endpoint en Laravel para manejar la subida
                chunk_size: '10mb',  // Tamaño de cada fragmento del archivo
                filters: {
                    mime_types: [
                        {title: "ZIP files", extensions: "zip"}
                    ]
                },
                multi_selection: false,  // No permitir selección múltiple
                flash_swf_url: "{{asset('js/Moxie.swf')}}",
                silverlight_xap_url: "{{asset('js/Moxie.xap')}}",
                init: {
                    FilesAdded: function(up, files) {
                        // Pasar los datos adicionales (paciente_id, tratamiento_id) en la carga
                        for (var i in files) {
                            files[i].url = '/upload?paciente_id={{$paciente->id}}&trat_id={{$selectedTratamiento}}';  // Agregar los parámetros
                        }
                    },
                    UploadComplete: function(up, files) {
                        // Callback cuando todos los fragmentos han sido subidos
                        console.log('Subida completada');
                    }
                }
            });

            uploader.init();
        </script>
    @endpush
</div>
