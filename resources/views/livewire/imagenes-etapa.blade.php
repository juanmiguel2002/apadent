<div>
    <h3 class="mt-2 font-semibold text-lg text-gray-800">Tratamiento: {{$paciente->tratamientos[0]->name . ' - '. $paciente->tratamientos[0]->descripcion}}</h3>
    <p class="mt-2">{{ $etapa->name }}</p>

    @if($archivos->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($archivos as $archivo)
                <div class="relative group">
                    <img src="{{ route('imagenes.protegidas', ['filePath' => $archivo->ruta]) }}"
                        class="h-48 w-full object-cover rounded-lg shadow-md transform group-hover:scale-105 transition-transform duration-300"
                        alt="Imagen">

                    <!-- Opción de mostrar la ruta o un título al pasar el ratón por encima -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <p class="text-white text-sm">{{ basename($archivo->ruta) }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="mt-2 text-gray-600">No hay archivos disponibles.</p>
    @endif
</div>
