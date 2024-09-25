<?php

namespace App\Http\Controllers;

use App\Models\Clinica;
use DragonCode\Support\Facades\Filesystem\File;
use Illuminate\Http\Request;

class ClinicaController extends Controller
{
    //Controlador de la clinica pasada por id
    public function index($id) {
        // Obtener la clínica especificada por el ID
        $clinica = Clinica::findOrFail($id);

        // Obtener solo los usuarios relacionados con esta clínica que son doctores
        $users = $clinica->users()->with('roles')->whereHas('roles', function($query) {
            $query->where('name', 'doctor'); // Filtrar por rol "doctor"
        })->get();

        return view('clinica', compact('clinica', 'users'));
    }
}
