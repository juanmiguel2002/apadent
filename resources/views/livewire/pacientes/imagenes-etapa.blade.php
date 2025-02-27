<div>
    @isset($etapa)
        <h3 class="mt-2 font-semibold text-lg text-gray-800">Tratamiento: {{$tratamiento->name . ' - '. $tratamiento->descripcion}}</h3>
        <p class="mt-2 font-semibold px-2">{{ $etapa->name }}</p>
    @endisset

    @if($archivos->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-2">
            @foreach ($archivos as $archivo)
                <div class="relative group">
                    <!-- Enlace para Lightbox -->
                    <a href="{{ route('imagenes.protegidas', ['filePath' => $archivo->ruta]) }}" target="_black" data-lightbox="gallery" data-title="{{ basename($archivo->ruta) }}">
                        <img src="{{ route('imagenes.protegidas', ['filePath' => $archivo->ruta]) }}"
                            class="h-49 w-full object-cover rounded-lg shadow-md transform group-hover:scale-105 transition-transform duration-300"
                            alt="Imagen">
                    </a>

                    <!-- Botón para descargar la imagen -->
                    <div class="absolute bottom-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <a href="{{ route('imagenes.protegidas', ['filePath' => $archivo->ruta]) }}" download class="bg-azul text-white px-3 py-1 rounded-lg shadow">
                            Descargar
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="mt-2 text-gray-600">No hay archivos disponibles.</p>
    @endif

</div>
