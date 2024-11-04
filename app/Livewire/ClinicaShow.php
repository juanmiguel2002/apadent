<?php

namespace App\Livewire;

use App\Mail\NuevaFactura;
use App\Models\Clinica;
use App\Models\Factura;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ClinicaShow extends Component
{
    use WithFileUploads, WithPagination;

    public $modalOpen = false;
    public $clinica, $clinicaName;
    public $users;
    public $name;
    public $factura;

    protected $rules = [
        'name' => 'required|string|max:50',
        'factura' => 'required|file|mimes:pdf', // Cambia los tipos mime según necesites
    ];

    public function mount($clinica, $users){
        $this->clinica = $clinica;
        $this->users = $users;
        // Sanitizar el nombre de la clínica para evitar problemas en la ruta
        $this->clinicaName = preg_replace('/\s+/', '_', trim($this->clinica->name));
    }

    public function render()
    {
        $facturas = Factura::where('clinica_id', $this->clinica->id)->paginate(6);
        return view('livewire.clinica-show', compact('facturas'));
    }

    public function openModal()
    {
        $this->modalOpen = true;
    }

    public function save()
    {
        $this->validate();

        // Obtener la extensión original del archivo
        $extension = $this->factura->getClientOriginalExtension();

        // Definir el nombre del archivo usando el nombre que el usuario introduzca
        $fileName = $this->name . '.' . $extension;

        // Definir la ruta de almacenamiento para la factura y guardar el archivo con el nombre del usuario
        $filePath = $this->factura->storeAs($this->clinicaName . '/facturas', $fileName , 'clinicas');

        // Guardar los detalles en la base de datos
        $factura = Factura::create([
            'name' => $this->name,
            'clinica_id' => $this->clinica->id,
            'user_id' => Auth::id(),
            'ruta' => $filePath,
        ]);

        $clinica = Clinica::find($this->clinica->id); // Obtener la clínica asociada al paciente
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
        if (Storage::disk('local')->exists($factura->ruta) && Auth::user()) {
            // Descargar el archivo
            return Storage::disk('local')->download($factura->ruta);
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
