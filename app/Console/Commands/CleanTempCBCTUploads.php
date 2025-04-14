<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CleanTempCBCTUploads extends Command
{
    protected $signature = 'cbct:clean-temp';
    protected $description = 'Elimina archivos temporales CBCT con más de 24 horas de antigüedad';

    public function handle()
    {
        $tempFolder = 'tmp/cbct_uploads';
        $disk = Storage::disk('local');

        if (!$disk->exists($tempFolder)) {
            $this->info("No existe la carpeta temporal.");
            return;
        }

        $files = $disk->files($tempFolder);
        $now = Carbon::now();

        $deleted = 0;

        foreach ($files as $file) {
            $fullPath = storage_path("app/{$file}");
            $lastModified = Carbon::createFromTimestamp(filemtime($fullPath));

            if ($now->diffInHours($lastModified) >= 24) {
                $disk->delete($file);
                $deleted++;
            }
        }

        $this->info("Archivos eliminados: {$deleted}");
    }
}
