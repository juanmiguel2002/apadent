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
        <p class="text-gray-600 mb-4">{{ $paciente->name }} {{ $paciente->apellidos }}</p>

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
        <div id="upload-success" class="hidden bg-green-500 text-white p-3 rounded-md mb-4"></div>
    </div>

    <script>
        let browseFile = $('#browseFile');

        let resumable = new Resumable({
            target: '{{ route('upload') }}',
            query: {
                _token: '{{ csrf_token() }}',
                pacienteId: {{ $paciente->id }},
                etapaId: {{ $etapa->id }}
            },
            fileType: ['zip'],
            chunkSize: 10 * 1024 * 1024,
            headers: {
                'Accept': 'application/json'
            },
            testChunks: false,
            throttleProgressCallbacks: 1,
        });

        let progressBar = document.getElementById("progress-bar");
        let progressContainer = document.getElementById("progress-container");

        resumable.assignBrowse(browseFile[0]);

        resumable.on('fileAdded', function (file) {
            progressContainer.classList.remove("hidden");
            resumable.upload();
        });

        resumable.on('fileProgress', function(file) {
            let percentage = Math.floor(file.progress() * 100);
            progressBar.style.width = percentage + "%";
            progressBar.innerText = percentage + "%";
        });

        resumable.on('fileSuccess', function(file, response) {
            let successDiv = document.getElementById("upload-success");
            successDiv.innerText = "Archivo subido exitosamente.";
            successDiv.classList.remove("hidden");

            progressBar.style.width = "0%";
            progressBar.innerText = "0%";
            resumable.removeFile(file);
            progressContainer.classList.add("hidden");

            setTimeout(() => window.history.back(), 2000);
        });

        resumable.on('fileError', function (file, message) {
            let errorData = JSON.parse(message);
            let errorDiv = document.getElementById("upload-error");
            errorDiv.innerText = errorData.message || "Error al subir el archivo.";
            errorDiv.classList.remove("hidden");
        });
    </script>
@endsection
