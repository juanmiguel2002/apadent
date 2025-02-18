<?php

namespace App\Livewire\Archivos;

use Livewire\Component;
use Illuminate\Support\Facades\File;

class FolderViewer extends Component
{
    public $currentPath; // Ruta actual
    public $parentPath;  // Ruta del directorio superiorww
    public $items = [];  // Archivos y subcarpetas

    public function mount($path = 'public')
    {
        $this->currentPath = storage_path("app/{$path}");
        $this->loadFolder();
    }

    public function loadFolder()
    {
        if (File::exists($this->currentPath)) {
            $this->items = collect(File::directories($this->currentPath))
                ->map(fn($folder) => [
                    'type' => 'folder',
                    'name' => basename($folder),
                    'path' => $folder,
                    'created_at' => File::lastModified($folder),
                ])->merge(
                    collect(File::files($this->currentPath))
                        ->map(fn($file) => [
                            'type' => 'file',
                            'name' => basename($file),
                            'path' => $file,
                            'size' => File::size($file),
                            'created_at' => File::lastModified($file),
                        ])
                )->toArray();

            $this->parentPath = dirname($this->currentPath);
        } else {
            $this->items = [];
        }
    }

    public function openFolder($path)
    {
        $this->currentPath = $path;
        $this->loadFolder();
    }

    public function goBack()
    {
        $this->currentPath = $this->parentPath;
        $this->loadFolder();
    }

    public function render()
    {
        return view('livewire.archivos.folder-viewer');
    }
}
