<?php

namespace App\Livewire;

use App\Models\Factura;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class SubirFactura extends Component
{
    use WithFileUploads, WithPagination;

    public $modalOpen = false;
    public $clinica;
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
    }

    public function render()
    {
        $facturas = Factura::where('clinica_id', $this->clinica->id)->paginate(5);
        return view('livewire.subir-factura', compact('facturas'));
    }

    public function openModal()
    {
        $this->modalOpen = true;
    }

    public function save()
    {
        $this->validate();

        $filePath = $this->factura->store('facturas', 'public');

        Factura::create([
            'name' => $this->name,
            'clinica_id' => $this->clinica->id,
            'user_id' => Auth::id(),
            'ruta' => $filePath,
        ]);

        $this->reset(['name', 'factura']);
        $this->dispatch('factura');
        $this->close();
    }

    public function close()
    {
        $this->modalOpen = false;
    }
}
