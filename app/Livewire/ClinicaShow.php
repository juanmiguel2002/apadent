<?php

namespace App\Livewire;

use App\Mail\NuevaFactura;
use App\Models\Carpeta;
use App\Models\Clinica;
use App\Models\Factura;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ClinicaShow extends Component
{
    use WithFileUploads, WithPagination;

    public Clinica $clinica;
    public User $user;
    public string $clinicaName = '';
    public $modalOpen = false;
    public $carpeta;

    public int $facturasPage = 1;
    public int $usersPage = 1;

    protected function queryString()
    {
        return [
            'facturasPage' => ['except' => 1],
            'usersPage' => ['except' => 1],
        ];
    }

    public function mount(Clinica $clinica, User $user)
    {
        $this->clinica = $clinica;
        $this->user = $user;
        $this->clinicaName = preg_replace('/\s+/', '_', trim($clinica->name));
    }

    public function render()
    {
        $facturas = Factura::where('clinica_id', $this->clinica->id)
            ->latest()
            ->paginate(6, ['*'], 'facturasPage', $this->facturasPage);

        $users = $this->clinica->users()
            ->with('roles')
            ->paginate(10, ['*'], 'usersPage', $this->usersPage);

        return view('livewire.clinica-show', compact('facturas', 'users'));
    }

    public function openModal()
    {
        $this->modalOpen = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:50',
            'factura' => 'required|file|mimes:pdf', // Cambia los tipos mime según necesites
        ]);

        // Obtener la extensión original del archivo
        $extension = $this->factura->getClientOriginalExtension();

        // Definir el nombre del archivo usando el nombre que el usuario introduzca
        $fileName = $this->name . '.' . $extension;

        // Definir la ruta de almacenamiento para la factura y guardar el archivo con el nombre del usuario
        $filePath = $this->factura->storeAs($this->clinicaName . '/facturas', $fileName , 'clinicas');

        $clinica = Clinica::findOrFail($this->clinica->id); // Obtener la clínica asociada

        // Buscar la carpeta "Facturas" dentro de la clínica específica
        $this->carpeta = Carpeta::where('nombre', 'Facturas')
                                ->where('clinica_id', $clinica->id)
                                ->firstOrFail(); // Asegura que la carpeta existe

        // Guardar los detalles en la base de datos
        $factura = Factura::create([
            'name' => $this->name,
            'ruta' => $filePath,
            'clinica_id' => $clinica->id,
            'user_id' => Auth::id(),
            'carpeta_id' => $this->carpeta->id
        ]);

        if ($clinica && $clinica->email) {
            Mail::to($clinica->email)->send(new NuevaFactura($clinica, $factura));
        }
        // Resetear los campos y cerrar el modal
        $this->reset(['name', 'factura']);
        $this->dispatch('factura');
        $this->close();
    }

    public function download(Factura $factura)
    {
        // Verificar si el usuario tiene acceso a la clínica
        if (Storage::disk('clinicas')->exists($factura->ruta) && Auth::user()) {
            // Descargar el archivo
            return Storage::disk('clinicas')->download($factura->ruta);
        }

        // Si el usuario no tiene acceso, devolver una respuesta de error
        return abort(403, 'No tienes permiso para ver este archivo.');
    }

    public function close()
    {
        $this->modalOpen = false;
        $this->reset(['name', 'factura']);
    }
}
