@extends('layouts.app')
@section('pageTitle', 'Crear Paciente')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Crear Paciente') }}
    </h2>
@endsection
@section('content')
    <div class="max-w-2xl mx-auto mt-10 shadow-md rounded-lg p-6 bg-white">
        <h2 class="text-2xl font-bold mb-6">Crear Paciente</h2>

        <form action="{{ route('paciente.create') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <span class="text-azul">{{$clinicas[0]->name}}</span>
                @if ($clinicaId)
                    <input type="hidden" name="clinica_id" value="{{$clinicaId}}">
                @endif
                @role('admin')
                    <div class="col-span-2 mb-4">
                        <x-label for="clinica_id" value="Clínica*" class="text-azul text-base"/>
                        <select name="clinica_id" class="form-input block w-full rounded-md border border-[rgb(224,224,224)]">
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
                    <x-input type="text" name="num_paciente" class="w-full rounded-md" placeholder="01" value="{{ old('num_paciente', $num_paciente ?? '')}}"/>
                    <x-input-error for="num_paciente" />
                </div>

                <div class="col-span-2 sm:col-span-1 mb-4">
                    <x-label for="name" value="Nombre*" class="text-azul text-base"/>
                    <x-input type="text" class="w-full rounded-md" name="name" placeholder="Nombre" value="{{ old('name', $name ?? '')}}"/>
                    <x-input-error for="name" />
                </div>

                <div class="col-span-2 sm:col-span-1 mb-4">
                    <x-label for="apellidos" value="Apellidos*" class="text-azul text-base"/>
                    <x-input type="text" class="w-full rounded-md" name="apellidos" placeholder="Apellidos" value="{{ old('apellidos', $apellidos ?? '')}}"/>
                    <x-input-error for="apellidos" />
                </div>

                <div class="col-span-2 sm:col-span-1 mb-4">
                    <x-label for="email" value="Email*" class="text-azul text-base"/>
                    <x-input type="email" class="w-full rounded-md" name="email" placeholder="strat@gmail.com" value="{{ old('email', $email ?? '')}}" />
                    <x-input-error for="email" />
                </div>

                <div class="col-span-2 sm:col-span-1 mb-4">
                    <x-label for="fecha_nacimiento" value="Fecha Nacimiento*" class="text-azul text-base"/>
                    <x-input type="date" id="fecha_nacimiento" class="w-full rounded-md" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $fecha_nacimiento ?? '')}}" />
                    <x-input-error for="fecha_nacimiento" />
                </div>

                <div class="col-span-2 sm:col-span-1 mb-4">
                    <x-label for="telefono" value="Teléfono*" class="text-azul text-base"/>
                    <x-input type="text" class="w-full rounded-md" name="telefono" placeholder="978456123" value="{{ old('telefono', $telefono ?? '')}}" />
                    <x-input-error for="telefono" />
                </div>

                <div class="col-span-2 sm:col-span-1 mb-4">
                    <x-label for="tratamiento_id" value="Tratamiento*" class="text-azul text-base"/>
                    <select name="selectedTratamiento" class="form-input block w-full rounded-md border border-[rgb(224,224,224)]">
                        <option value="">Seleccione un Tratamiento</option>
                        @foreach($tratamientos as $tratamiento)
                        <option value="{{ $tratamiento->id }}">{{ $tratamiento->name }} - {{ $tratamiento->descripcion}}</option>
                        @endforeach
                    </select>
                    <x-input-error for="selectedTratamiento" />
                </div>

                <div class="col-span-2 mb-4">
                    <x-label for="observacion" value="Observaciones" class="text-azul text-md"/>
                    <textarea name="observacion" class="w-full rounded-md border border-[rgb(224,224,224)] resize-none" rows="4" ></textarea>
                    <x-input-error for="observacion" />
                </div>

                <div class="col-span-2 mb-3">
                    <x-label for="img_paciente" class="block text-md text-azul capitalize">Foto paciente</x-label>
                    <x-input name="img_paciente" type="file" class="block w-full px-3 py-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-300 focus:ring-opacity-40" />
                    <x-input-error for="img_paciente" />
                </div>

                <div class="col-span-2 mb-3">
                    <x-label for="imagenes" class="block text-md text-azul capitalize">Fotografías</x-label>
                    <x-input type="file" name="imagenes[]" multiple class="block w-full px-3 py-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-300 focus:ring-opacity-40" />
                    <x-input-error for="imagenes.*" />
                </div>

                <div class="col-span-2 mb-3">
                    <x-label for="rayos" class="block text-md text-azul capitalize">Archivos RX</x-label>
                    <x-input type="file" name="rayos[]" multiple class="block w-full px-3 py-2 text-gray-600 placeholder-gray-400 bg-white border border-gray-200 rounded-md focus:border-indigo-400 focus:outline-none focus:ring focus:ring-indigo-300 focus:ring-opacity-40" />
                </div>

                <div class="col-span-2 mb-3">
                    <div id="upload-container">
                        <x-label for="cbct" value="Subida de CBCT" class="text-base"/>
                        <button id="browseFile" type="button" class="bg-azul hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded mt-1">
                            Seleccionar Archivo
                        </button>
                    </div>
                    <div id="progress-container" class="hidden mt-3 w-full bg-gray-200 rounded overflow-hidden">
                        <div id="progress-bar" class="h-6 bg-azul text-white text-center text-sm font-semibold" style="width: 0%;">
                            0%
                        </div>
                    </div>
                    <br>
                    <div id="upload-error" class="hidden bg-red-500 text-white p-3 rounded-md mb-4"></div>
                    <div id="upload-success" class="hidden bg-green-500 text-white p-3 rounded-md mb-4"></div>
                    <!-- Campo oculto para almacenar el path temporal -->
                    <input type="hidden" id="cbct_temp_path" name="cbct_temp_path" value="{{ old('cbct_temp_path') }}">
                </div>

                <div class="col-span-2 mb-4">
                    <x-label for="obser_cbct" value="Observaciones Cbct/Imágenes" class="text-azul text-base"/>
                    <textarea name="obser_cbct" class="w-full rounded-md border border-[rgb(224,224,224)] resize-none" rows="4" placeholder="Definir medididas, intraorales o físicas"></textarea>
                    <x-input-error for="obser_cbct" />
                </div>
            </div>
            <button type="submit" id="crearPacienteBtn" class="mt-6 bg-azul text-white px-4 py-2 rounded">Guardar Paciente</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const progressBar = document.getElementById('progress-bar');
            const progressContainer = document.getElementById('progress-container');
            const uploadSuccess = document.getElementById('upload-success');
            const uploadError = document.getElementById('upload-error');
            const cbctTempPathInput = document.getElementById('cbct_temp_path');
            const crearPacienteBtn = document.getElementById('crearPacienteBtn');

            const r = new Resumable({
                target: '{{ route("paciente.upload") }}',
                chunkSize: 10 * 1024 * 1024, // 10MB
                simultaneousUploads: 3,
                testChunks: false,
                fileType: ['zip'],
                throttleProgressCallbacks: 1,
                query: {
                    _token: '{{ csrf_token() }}'
                }
            });

            r.assignBrowse(document.getElementById('browseFile'));

            r.on('fileAdded', function () {
                progressContainer.classList.remove('hidden');
                progressBar.style.width = '0%';
                progressBar.innerText = '0%';
                uploadSuccess.classList.add('hidden');
                uploadError.classList.add('hidden');
                crearPacienteBtn.disabled = true; // ❌ Deshabilitar botón
                r.upload();
            });

            r.on('fileProgress', function (file) {
                const percent = Math.floor(file.progress() * 100);
                progressBar.style.width = percent + '%';
                progressBar.innerText = percent + '%';
            });

            r.on('fileSuccess', function (file, response) {
                try {
                    const res = JSON.parse(response);

                    if (res.success && res.temp_path) {
                        uploadSuccess.classList.remove('hidden');
                        uploadSuccess.innerText = "Archivo CBCT subido correctamente.";
                        cbctTempPathInput.value = res.temp_path;
                        uploadError.classList.add('hidden');
                        crearPacienteBtn.disabled = false; // ✅ Habilitar botón
                    } else {
                        throw new Error("Respuesta inesperada del servidor.");
                    }
                } catch (err) {
                    uploadError.classList.remove('hidden');
                    uploadError.innerText = "Error al procesar la respuesta del servidor.";
                    uploadSuccess.classList.add('hidden');
                    crearPacienteBtn.disabled = false; // ✅ Habilitar aunque haya error
                }
            });

            r.on('fileError', function () {
                uploadError.classList.remove('hidden');
                uploadError.innerText = "Error al subir el archivo. Inténtalo de nuevo.";
                uploadSuccess.classList.add('hidden');
                crearPacienteBtn.disabled = false; // ✅ Habilitar en error
            });
        });

    </script>
@endsection
