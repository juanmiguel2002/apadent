@extends('layouts.app')
@section('pageTitle', 'Subir CBCT')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Subir CBCT') }}
    </h2>
@endsection
@section('content')
    <div class="max-w-lg mx-auto p-6 bg-white rounded shadow-lg">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Subir CBCT (.zip)</h3>
        <p class="text-gray-600 mb-4">{{$paciente->name}} {{$paciente->apellidos}}</p>

        <div id="upload-container" class="text-center">
            <button id="browseFile" class="bg-azul hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
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
    </div>

    <script type="text/javascript">
        let browseFile = $('#browseFile');

        let resumable = new Resumable({

            target: '{{ route('upload') }}', // El endpoint para la subida
            query: {
                _token: '{{ csrf_token() }}',
                paciente: '{{ $paciente->id }}',  // El ID del paciente
                etapa: '{{ $etapa->id }}'  // El ID de la etapa
            },
            fileType: ['zip'], // Solo permite archivos .zip
            chunkSize: 10 * 1024 * 1024, // Tamaño de chunk de 10MB (ajustable)
            headers: {
                'Accept': 'application/json'
            },
            testChunks: false, // Desactiva la prueba de chunks
            throttleProgressCallbacks: 1, // Para el progreso
        });

        let progressBar = document.getElementById("progress-bar");
        let progressContainer = document.getElementById("progress-container");

        resumable.assignBrowse(browseFile[0]);

        resumable.on('fileAdded', function (file) {
            progressContainer.classList.remove("hidden");
            resumable.upload(); // Comienza la carga
        });

        resumable.on('fileProgress', function(file) {
            let percentage = Math.floor(file.progress() * 100);
            progressBar.style.width = percentage + "%";
            progressBar.innerText = percentage + "%";
        });
        resumable.on('fileError', function (file, response) {
            try {
                let errorData = JSON.parse(response);
                let errorDiv = document.getElementById("upload-error");
                errorDiv.innerText = errorData.error;
                errorDiv.classList.remove("hidden");
            } catch (e) {
                console.error("Error al procesar la respuesta:", e);
            }
        });
    </script>
@endsection
