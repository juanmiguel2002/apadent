<?php

namespace App\Http\Controllers;

use App\Models\Archivo;
use App\Models\Carpeta;
use App\Models\Clinica;
use App\Models\Etapa;
use App\Models\Paciente;
use App\Models\Tratamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\ResumableJSUploadHandler;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class UploadController extends Controller
{
    //
    public function index(Request $request)
    {
        $pacienteId = $request->pacienteId;
        $etapaId = $request->etapaId;
        $paciente = Paciente::find($pacienteId);
        $etapa = Etapa::find($etapaId);

        return view('upload', ['paciente' => $paciente, 'etapa' => $etapa]);
    }

    public function upload(Request $request)
    {
        // Obtener IDs de la URL
        $etapaId = $request->query('etapaId');
        $pacienteId = $request->query('pacienteId');

        if (!$pacienteId || !$etapaId) {
            return response()->json(['error' => 'Faltan datos de paciente o etapa'], 400);
        }

        // Inicializar Resumable.js
        $receiver = new FileReceiver("file", $request, ResumableJSUploadHandler::class);

        if (!$receiver->isUploaded()) {
            throw new UploadMissingFileException();
        }

        $save = $receiver->receive();

        // Obtener el paciente y la etapa desde la BD
        $paciente = Paciente::find($pacienteId);
        $clinica = Clinica::find($paciente->clinica_id);
        $etapa = Etapa::find($etapaId);
        $tratamiento = Tratamiento::find($etapa->trat_id);

        $nombreClinica = preg_replace('/\s+/', '_', trim($clinica->name));
        $primerApellido = strtok($paciente->apellidos, " ");
        $tratName = preg_replace('/\s+/', '_', trim($tratamiento->name .' '. $tratamiento->descripcion));
        $tratBBDD = $tratamiento->name .' '. $tratamiento->descripcion;

        $nombrePaciente = preg_replace('/\s+/', '_', trim($paciente->name . ' ' . $primerApellido . ' ' . $paciente->num_paciente));
        $nombreP = $paciente->name . ' ' . $primerApellido . '_' . $paciente->num_paciente;

        // Ruta de la carpeta del paciente
        $pacienteFolder = "{$nombreClinica}/pacientes/{$nombrePaciente}/{$tratName}";

        if (!$paciente || !$etapa) {
            return response()->json(['error' => 'Paciente o Etapa no encontrada'], 400);
        }

        // Buscar la carpeta del paciente
        $carpetaPaciente = Carpeta::where('nombre', $nombreP)
            ->whereHas('parent', fn($query) => $query->where('nombre', 'pacientes'))
            ->first();

        if (!$carpetaPaciente) {
            return response()->json(['error' => 'Carpeta del paciente no encontrada'], 400);
        }

        $carpetaTratamiento = Carpeta::firstOrCreate([
            'nombre'      => $tratBBDD,
            'carpeta_id'  => $carpetaPaciente->id
        ]);

        // Buscar o crear la carpeta CBCT dentro del paciente
        $carpetaCBCT = Carpeta::firstOrCreate([
            'nombre'      => 'CBCT',
            'carpeta_id'  => $carpetaTratamiento->id,
            'clinica_id' => $clinica->id
        ]);

        if ($save->isFinished()) {
            $file = $save->getFile();

            $filename = $etapa->name . '_' . $file->getClientOriginalName();
            $filePath = "{$pacienteFolder}/CBCT/{$filename}";

            Storage::disk('clinicas')->putFileAs("{$pacienteFolder}/CBCT", $file, $filename);
            unlink($file->getPathname());

            // Guardar el archivo en la base de datos
            Archivo::create([
                'name'       => pathinfo($filename, PATHINFO_FILENAME),
                'ruta'       => $filePath,
                'tipo'       => 'cbct',
                'extension'  => $file->getClientOriginalExtension(),
                'etapa_id'   => $etapaId,
                'carpeta_id' => $carpetaCBCT->id,
                'paciente_id' => $pacienteId,
            ]);
            // return redirect()->route('paciente-historial', ['id' => $pacienteId])->with('error', 'Error al subir CBCT.');

        }

        $handler = $save->handler();
        return redirect()->route('paciente-historial', ['id' => $pacienteId])->with('error', 'Error al subir CBCT.');

    }
}
