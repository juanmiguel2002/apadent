<?php

namespace App\Http\Controllers\Pacientes;

use App\Http\Controllers\Controller;
use App\Mail\NotificacionNuevoPaciente;
use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\Clinica;
use App\Models\Etapa;
use App\Models\Fase;
use App\Models\Paciente;
use App\Models\PacienteTrat;
use App\Models\Tratamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\ResumableJSUploadHandler;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class Pacientes extends Controller
{
    //
    public function index() {

        return view('pacientes.index');
    }

    public function show()
    {
        // Traemos todos los tratamientos
        $tratamientos = Tratamiento::all();

        // Si el usuario tiene el rol de admin, pasamos todas las clínicas
        if (Auth::user()->hasRole('admin')) {
            $clinicas = Clinica::all();
        } else {
            // Si no es admin, solo pasamos la clínica asociada al usuario
            $clinicaId = Auth::user()->clinicas->first()->id;
            $clinicas = Clinica::where('id', $clinicaId)->get();  // Devuelves solo la clínica asociada
        }

        // Pasamos las variables a la vista
        return view('pacientes.create', [
            'clinicas' => $clinicas,
            'tratamientos' => $tratamientos,
            'clinicaId' => isset($clinicaId) ? $clinicaId : null // Si es admin, se pasa null para clinicaId
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'clinica_id' => 'required|exists:clinicas,id',
            'num_paciente' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Paciente::where('clinica_id', $request->clinica_id)  // Usamos el $request aquí
                                      ->where('num_paciente', $value)
                                      ->exists();
                    if ($exists) {
                        $fail('El número de paciente ya está registrado en esta clínica.');
                    }
                },
            ],
            'name' => 'required|string',
            'apellidos' => 'required|string',
            'fecha_nacimiento' => 'required|date',
            'email' => 'required|email',
            'telefono' => 'required|string',
            'observacion' => 'nullable|string',
            'obser_cbct' => 'nullable|string',
            'selectedTratamiento' => 'required|exists:tratamientos,id',
            'img_paciente' => 'nullable|file|image',
        ]);

        DB::transaction(function () use ($request) {
            $clinicaId = $request->clinica_id ?? Auth::user()->clinicas->first()->id;

            // 1. Crear paciente
            $paciente = Paciente::create([
                'num_paciente' => $request->num_paciente,
                'name' => $request->name,
                'apellidos' => $request->apellidos,
                'fecha_nacimiento' => $request->input('fecha_nacimiento'),
                'email' => $request->email,
                'telefono' => $request->telefono,
                'observacion' => $request->observacion,
                'obser_cbct' => $request->obser_cbct,
                'clinica_id' => $clinicaId,
            ]);

            // 2. Asociar tratamiento
            PacienteTrat::create([
                'paciente_id' => $paciente->id,
                'trat_id' => $request->selectedTratamiento
            ]);

            // 3. Crear etapas iniciales
            $fases = Fase::where('trat_id', $request->selectedTratamiento)->get();
            foreach ($fases as $fase) {
                $etapa = Etapa::firstOrCreate(
                    [
                        'fase_id' => $fase->id,
                        'paciente_id' => $paciente->id,
                        'trat_id' => $request->selectedTratamiento,
                    ],
                    [
                        'name' => 'Inicio',
                        'fecha_ini' => now(),
                        'status' => 'Set Up',
                    ]
                );
            }

            // 4. Crear carpetas del paciente
            $pacienteFolder = $this->createPacienteFolders($paciente, $request->selectedTratamiento, $clinicaId);

            // 5. Subir imagen del paciente (si hay)
            if ($request->hasFile('img_paciente')) {
                $img = $request->file('img_paciente');
                $fileName = 'foto_' . $paciente->name . '.' . $img->getClientOriginalExtension();
                $path = $img->storeAs($pacienteFolder . '/fotoPaciente', $fileName, 'public');

                $paciente->url_img = $path;
                $paciente->save();
            }
            $rayos = $request->file('rayos');
            $imagenes = $request->file('imagenes');
            $tempPath = $request->input('cbct_temp_path');
            
            // 6. Subida de archivos adicionales
            $this->guardarArchivosEtapa($tempPath, $imagenes, $rayos, $paciente, $etapa, $request->selectedTratamiento, $pacienteFolder);

            // 7. Notificar por email
            $clinica = Clinica::find($paciente->clinica_id);
            if ($clinica && $clinica->email) {
                Mail::to($clinica->email)->send(
                    new NotificacionNuevoPaciente($clinica, $paciente, $clinica, route('pacientes-show', $paciente->id))
                );
            }
        });

        return redirect()->route('dashboard')->with('success', 'Paciente creado correctamente.');
    }

    protected function createPacienteFolders($paciente, $tratId, $clinicaId = null)
    {
        // Obtener clínica
        $clinica = $clinicaId
            ? Clinica::find($clinicaId)
            : Auth::user()->clinicas->first();

        if (!$clinica) {
            throw new \Exception("No se encontró ninguna clínica asociada.");
        }

        $tratamiento = Tratamiento::find($tratId);
        if (!$tratamiento) {
            throw new \Exception("Tratamiento no encontrado.");
        }

        // Nombres para la base de datos
        $primerApellido = strtok($paciente->apellidos, ' ');
        $tratBbdd = $tratamiento->name . ' ' . $tratamiento->descripcion;
        $nombrePaciente = $paciente->name . ' ' . $primerApellido . '_' . $paciente->num_paciente;

        // Normalizar nombres
        $nombreClinica = preg_replace('/\s+/', '_', trim($clinica->name));
        $nombreP = preg_replace('/\s+/', '_', trim($paciente->name . ' ' . $primerApellido . ' ' . $paciente->num_paciente));
        $tratName = preg_replace('/\s+/', '_', trim($tratamiento->name . ' ' . $tratamiento->descripcion));

        // Rutas
        $pacienteBaseFolder = "{$nombreClinica}/pacientes/{$nombreP}";
        $tratamientoFolder = "{$pacienteBaseFolder}/{$tratName}";
        $strippingFolder = "{$pacienteBaseFolder}/Stripping";

        // Crear carpetas físicas
        $disk = Storage::disk('clinicas');
        if (!$disk->exists($pacienteBaseFolder)) {
            $disk->makeDirectory($pacienteBaseFolder);
        }
        if (!$disk->exists($tratamientoFolder)) {
            $disk->makeDirectory($tratamientoFolder);
        }
        if (!$disk->exists($strippingFolder)) {
            $disk->makeDirectory($strippingFolder);
        }

        // Crear carpetas en BBDD
        $carpetaClinica = Carpeta::firstOrCreate(['nombre' => $clinica->name, 'carpeta_id' => null]);
        $carpetaPacientes = Carpeta::firstOrCreate([
            'nombre' => 'pacientes',
            'carpeta_id' => $carpetaClinica->id,
            'clinica_id' => $clinica->id,
        ]);
        $carpetaPaciente = Carpeta::firstOrCreate([
            'nombre' => $nombrePaciente,
            'carpeta_id' => $carpetaPacientes->id,
            'clinica_id' => $clinica->id,
        ]);

        $carpetaTratamiento = Carpeta::firstOrCreate([
            'nombre' => $tratBbdd,
            'carpeta_id' => $carpetaPaciente->id,
            'clinica_id' => $clinica->id,
        ]);

        // Subcarpetas del tratamiento
        $subFolders = ['imgEtapa', 'CBCT', 'archivoComplementarios', 'Rayos'];
        foreach ($subFolders as $subFolder) {
            $path = "{$tratamientoFolder}/{$subFolder}";
            if (!$disk->exists($path)) {
                $disk->makeDirectory($path);
            }

            Carpeta::firstOrCreate([
                'nombre' => $subFolder,
                'carpeta_id' => $carpetaTratamiento->id,
                'clinica_id' => $clinica->id,
            ]);
        }

        // Subcarpeta Stripping (directamente en carpetaPaciente)
        Carpeta::firstOrCreate([
            'nombre' => 'Stripping',
            'carpeta_id' => $carpetaPaciente->id,
            'clinica_id' => $clinica->id,
        ]);

        return $pacienteBaseFolder;
    }

    protected function guardarArchivosEtapa($tempPath, $imagenes, $rayos, $paciente, $etapa, $tratId, $pacienteFolder)
    {
        $tratamiento = Tratamiento::find($tratId);
        if (!$tratamiento) {
            throw new \Exception('Tratamiento no encontrado.');
        }

        $tratBbdd = $tratamiento->name . ' ' . $tratamiento->descripcion;
        $tratName = preg_replace('/\s+/', '_', trim($tratBbdd));

        // Buscar la carpeta del paciente dentro de 'pacientes'
        $primerApellido = strtok($paciente->apellidos, ' ');
        $nombrePaciente = $paciente->name . ' ' . $primerApellido . '_' . $paciente->num_paciente;

        $carpetaPaciente = Carpeta::where('nombre', $nombrePaciente)
            ->whereHas('parent', function ($query) {
                $query->where('nombre', 'pacientes');
            })->first();

        if (!$carpetaPaciente) {
            throw new \Exception('Carpeta del paciente no encontrada.');
        }

        // Buscar o crear carpeta de tratamiento
        $carpetaTratamiento = Carpeta::firstOrCreate([
            'nombre' => $tratBbdd,
            'carpeta_id' => $carpetaPaciente->id,
        ]);

        // Subcarpetas y archivos
        $subCarpetas = [
            'imgEtapa' => $imagenes,
            'Rayos' => $rayos,
        ];

        foreach ($subCarpetas as $nombreCarpeta => $archivos) {

            $carpeta = Carpeta::where('nombre', $nombreCarpeta)
                ->where('carpeta_id', $carpetaTratamiento->id)
                ->first();

            if (!$carpeta) {
                throw new \Exception("Carpeta {$nombreCarpeta} no encontrada.");
            }
            if (!$archivos) {
                continue; // Si no hay archivos, saltar a la siguiente carpeta
            }
            foreach ($archivos as $key => $archivo) {
                $fileName = "{$etapa->name}_" . ($key + 1) . "_{$nombreCarpeta}." . $archivo->getClientOriginalExtension();
                $fileName = preg_replace('/[^\w.-]/', '_', $fileName);

                $relativePath = "{$pacienteFolder}/{$tratName}/{$nombreCarpeta}";
                $fullPath = "{$relativePath}/{$fileName}";

                Storage::disk('clinicas')->putFileAs($relativePath, $archivo, $fileName);

                Archivo::create([
                    'name' => pathinfo($fileName, PATHINFO_FILENAME),
                    'ruta' => $fullPath,
                    'tipo' => strtolower($nombreCarpeta),
                    'extension' => $archivo->getClientOriginalExtension(),
                    'etapa_id' => $etapa->id,
                    'carpeta_id' => $carpeta->id,
                    'paciente_id' => $paciente->id,
                ]);

                if (file_exists($archivo->getPathname())) {
                    unlink($archivo->getPathname());
                }
            }

        }

        // Subida de CBCT
        $clinica = Clinica::find($paciente->clinica_id);
        if (!$clinica) {
            throw new \Exception('Clinica no encontrada.');
        }

        if ($tempPath && Storage::disk('local')->exists($tempPath)) {

            // Obtener el archivo desde la carpeta temporal
            $tempFile = Storage::disk('local')->path($tempPath);

            $filename = $etapa->name . '_' . basename($tempPath);

            $nombreClinica = preg_replace('/\s+/', '_', trim($clinica->name));
            $primerApellido = strtok($paciente->apellidos, " ");
            $tratName = preg_replace('/\s+/', '_', trim($tratamiento->name .' '. $tratamiento->descripcion));
            $nombrePaciente = preg_replace('/\s+/', '_', trim($paciente->name . ' ' . $primerApellido . ' ' . $paciente->num_paciente));
            $pacienteFolder = "{$nombreClinica}/pacientes/{$nombrePaciente}/{$tratName}";

            // Crear carpeta CBCT
            $carpetaCBCT = Carpeta::firstOrCreate([
                'nombre'      => 'CBCT',
                'carpeta_id'  => $carpetaTratamiento->id,
                'clinica_id'  => $clinica->id
            ]);

            // Mover archivo
            Storage::disk('clinicas')->putFileAs("{$pacienteFolder}/CBCT", new \Illuminate\Http\File($tempFile), $filename);

            // Eliminar archivo temporal
            Storage::disk('local')->delete($tempPath);

            // Guardar en BBDD
            Archivo::create([
                'name'        => pathinfo($filename, PATHINFO_FILENAME),
                'ruta'        => "{$pacienteFolder}/CBCT/{$filename}",
                'tipo'        => 'cbct',
                'extension'   => pathinfo($filename, PATHINFO_EXTENSION),
                'etapa_id'    => $etapa->id,
                'carpeta_id'  => $carpetaCBCT->id,
                'paciente_id' => $paciente->id,
            ]);
        }
    }

    public function upload(Request $request)
    {
        $receiver = new FileReceiver("file", $request, ResumableJSUploadHandler::class);

        if (!$receiver->isUploaded()) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        if ($save->isFinished()) {
            $file = $save->getFile();
            $filename = uniqid() . '_' . $file->getClientOriginalName(); // Evita colisiones

            // Carpeta temporal dentro de storage/app/tmp/cbct_uploads
            $tempFolder = 'tmp/cbct_uploads';

            // Crear la carpeta si no existe
            if (!Storage::disk('local')->exists($tempFolder)) {
                Storage::disk('local')->makeDirectory($tempFolder);
            }

            // Guardar el archivo en la carpeta temporal
            $tempPath = "{$tempFolder}/{$filename}";
            Storage::disk('local')->putFileAs($tempFolder, $file, $filename);
            unlink($file->getPathname());

            return response()->json([
                'success' => true,
                'temp_path' => $tempPath,
                'original_name' => $file->getClientOriginalName(),
                'message' => 'Archivo temporal subido correctamente.'
            ]);
        }

        return response()->json(['chunk_received' => true]);
    }
}
