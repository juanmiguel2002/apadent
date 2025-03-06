<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class LimpiarArchivosTemporales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archivos:limpiar-temp';
    protected $description = 'Elimina archivos temporales viejos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directorio = 'storage/livewire-tmp/';
        $archivos = Storage::files($directorio);

        foreach ($archivos as $archivo) {
            // Verificar si el archivo tiene más de 1 día
            if (now()->diffInDays(Storage::lastModified($archivo)) > 1) {
                Storage::delete($archivo);
            }
        }

        $this->info('Archivos temporales eliminados.');
    }
}
