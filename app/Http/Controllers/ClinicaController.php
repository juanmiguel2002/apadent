<?php

namespace App\Http\Controllers;

use App\Models\Clinica;
use Illuminate\Http\Request;

class ClinicaController extends Controller
{
    //Controlador de la clinica pasada por id
    public function index($id) {
        // Obtener la clínica especificada por el ID
        $clinica = Clinica::findOrFail($id);

        // Obtener los usuarios relacionados con esta clínica
        $users = $clinica->users;

        return view('clinica', compact('clinica', 'users'));
    }
}
