<?php

namespace App\Livewire\Archivos;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Dashboard extends Component
{
    public $currentPath = '';
    public $directories = [];
    public $files = [];
    public $newFolderName = '';
    protected $basePath = 'clinicas';

    public function mount()
    {
        if (Auth::user()->hasRole('admin')) {
            $this->currentPath = $this->basePath;
        } else {
            // Obtener la clínica asignada al usuario
            $clinica = Auth::user()->clinicas->first(); // Ajusta según la relación
            $this->currentPath = $this->basePath . '/' . $clinica->name;
        }
        $this->loadContents();
    }

    public function loadContents()
    {
        $disk = Storage::disk('local');
        // Listar carpetas
        $this->directories = collect($disk->directories($this->currentPath))
        ->filter(fn($directory) => basename($directory) !== 'livewire-tmp')
        ->map(function ($directory) use ($disk) {
            return [
                'name' => basename($directory),
                'path' => $directory,
                'fileCount' => count($disk->files($directory)),
                'lastModified' => \Carbon\Carbon::createFromTimestamp($disk->lastModified($directory))->format('d/m/Y H:i'),
            ];
        })->toArray();

        // Listar archivos
        $this->files = collect(Storage::disk('local')->files($this->currentPath))->map(function ($file) {
            return [
                'name' => basename($file),
                'url' => Storage::disk('clinicas')->url($file),
                'lastModified' => \Carbon\Carbon::createFromTimestamp(Storage::disk('local')->lastModified($file))->format('d/m/Y H:i'),
                'extension' => pathinfo($file, PATHINFO_EXTENSION),
            ];
        })->toArray();
        // dd($this->currentPath);
    }

    public function navigateTo($folderName)
    {
        // No permitir subir más allá del punto inicial
        $newPath = trim($this->currentPath . '/' . $folderName, '/');
        if (str_starts_with($newPath, $this->basePath)) {
            $this->currentPath = $newPath;
            $this->loadContents();
            $this->dispatch('pathChanged', $this->currentPath);
        }
    }

    public function navigateBack()
    {
        if (Auth::user()->hasRole('doctor_admin')) {
            $clinic = Auth::user()->clinicas->first();
            $clinicBasePath = "$this->basePath/{$clinic->name}";

            // Si ya estás en la carpeta base, no se puede ir más atrás.
            if ($this->currentPath === $clinicBasePath) {
                return;
            }
        } elseif (Auth::user()->hasRole('admin') && $this->currentPath === $this->basePath) {
            // Si eres admin y estás en la raíz, no se puede ir más atrás.
            return;
        }

        // Retrocede un nivel en la jerarquía de carpetas.
        $this->currentPath = dirname($this->currentPath) === '.' ? '' : dirname($this->currentPath);
        $this->loadContents();
    }

    public function createFolder()
    {
        if (!empty($this->newFolderName)) {
            Storage::disk('local')->makeDirectory("{$this->currentPath}/{$this->newFolderName}");
            $this->newFolderName = '';
            $this->loadContents();
        }
    }

    public function delete($name)
    {
        $targetPath = "{$this->currentPath}/{$name}";
        if (Storage::disk('local')->exists($targetPath)) {
            if (Storage::disk('local')->deleteDirectory($targetPath)) {
                $this->loadContents();
            }
        }
    }

    public function render()
    {
        return view('livewire.archivos.index');
    }

}
