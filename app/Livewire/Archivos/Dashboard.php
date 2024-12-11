<?php

namespace App\Livewire\Archivos;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Dashboard extends Component
{
    public $currentPath = '';
    public $directories = [];
    public $files = [];
    public $newFolderName = '';
    protected $baseDirectory = 'clinicas';

    public function mount()
    {
        if (Auth::user()->hasRole('admin')) {
            $this->currentPath = 'clinicas';
        } elseif (Auth::user()->hasRole('doctor_admin')) {
            $clinic = Auth::user()->clinicas->first(); // Asume que un usuario doctor_admin está asociado a una clínica.
            $this->currentPath = "clinicas/{$clinic->name}";
        }

        $this->loadContents();
    }

    public function loadContents()
    {
        // Listar carpetas
        $this->directories = collect(Storage::disk('local')->directories($this->currentPath))
            ->filter(function ($directory) {
                return basename($directory) !== 'livewire-tmp'; // Excluye "livewire-tmp"
            })
            ->map(function ($directory) {
                $filesInFolder = Storage::disk('local')->files($directory);
                return [
                    'name' => basename($directory),
                    'path' => $directory,
                    'fileCount' => count($filesInFolder),
                    'lastModified' => \Carbon\Carbon::createFromTimestamp(Storage::disk('local')->lastModified($directory))->format('d/m/Y H:i'),
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
        $this->currentPath = trim("{$this->currentPath}/{$folderName}", '/');
        $this->loadContents();
    }

    public function navigateBack()
    {
        if (Auth::user()->hasRole('doctor_admin')) {
            $clinic = Auth::user()->clinicas->first();
            $clinicBasePath = "$this->baseDirectory/{$clinic->name}";

            // Si ya estás en la carpeta base, no se puede ir más atrás.
            if ($this->currentPath === $clinicBasePath) {
                return;
            }
        } elseif (Auth::user()->hasRole('admin') && $this->currentPath === $this->baseDirectory) {
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
