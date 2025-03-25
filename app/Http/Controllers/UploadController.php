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
    public $pacienteId, $etapaId;
    public $paciente, $etapa;
    public function index(Request $request)
    {
        $this->pacienteId = $request->pacienteId;
        $this->etapaId = $request->etapaId;
        $this->paciente = Paciente::find($this->pacienteId);
        $this->etapa = Etapa::find($this->etapaId);

        return view('upload', ['paciente' => $this->paciente, 'etapa' => $this->etapa]);
    }

    public function upload(Request $request)
    {
        // Obtener IDs de la URL
        $pacienteId = $request->query('paciente');
        $etapaId = $request->query('etapa');

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
        $etapa = Etapa::find($etapaId);

        if (!$paciente || !$etapa) {
            return response()->json(['error' => 'Paciente o Etapa no encontrados'], 404);
        }

        // Buscar la carpeta del paciente
        $carpetaPaciente = Carpeta::where('nombre', $paciente->name)
            ->whereHas('parent', fn($query) => $query->where('nombre', 'pacientes'))
            ->first();

        if (!$carpetaPaciente) {
            return response()->json(['error' => 'Carpeta del paciente no encontrada'], 404);
        }

        // Buscar o crear la carpeta CBCT dentro del paciente
        $carpetaCBCT = Carpeta::firstOrCreate([
            'nombre'      => 'CBCT',
            'carpeta_id'  => $carpetaPaciente->id
        ]);

        // Obtener información de la clínica
        $clinica = Clinica::find($paciente->clinica_id);
        $clinicaName = preg_replace('/\s+/', '_', trim($clinica->name));
        $pacienteName = preg_replace('/\s+/', '_', trim($paciente->name . ' ' . $paciente->apellidos));

        $pacienteFolder = "{$clinicaName}/pacientes/{$pacienteName}";

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

            return response()->json([
                'message' => 'Archivo subido con éxito',
                'path' => $filePath
            ]);
        }

        $handler = $save->handler();
        return response()->json([
            "done" => $handler->getPercentageDone(),
            "status" => true
        ]);
    }

    public function upload2(Request $request)
    {
        // Validar que el archivo es de tipo .zip
        $request->validate([
            'file' => 'required|mimes:zip|max:2048000',  // 2GB máximo
        ]);

        // Obtener los datos del paciente, tratamiento, y clínica
        $paciente_id = $request->input('paciente_id');
        $tratamiento_id = $request->input('trat_id');

        // Buscar al paciente y tratamiento por sus IDs
        $paciente = Paciente::findOrFail($paciente_id);
        $clinica = Clinica::find($paciente->clinica_id);
        $tratamiento = Tratamiento::findOrFail($tratamiento_id);

        $clinicaName = preg_replace('/\s+/', '_', trim($clinica->name));
        $pacienteName = preg_replace('/\s+/', '_', trim($paciente->name . ' ' . $paciente->apellidos));

        $pacienteFolder = "{$clinicaName}/pacientes/{$pacienteName}";

        // Verificar si la carpeta existe, si no, crearla
        if (!Storage::exists($path)) {
            Storage::makeDirectory($path);
        }

        // Subir el archivo
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();

        // Guardar el archivo en la ruta definida
        $filePath = Storage::putFileAs($path, $file, $fileName);

        return response()->json(['path' => $filePath], 200);
    }

}
