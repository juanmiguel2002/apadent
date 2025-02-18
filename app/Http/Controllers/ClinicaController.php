<?php

namespace App\Http\Controllers;

use App\Models\Clinica;

class ClinicaController extends Controller
{
    //Controlador de la clinica pasada por id
    public function index($id) {
        // Obtener la clínica especificada por el ID
        $clinica = Clinica::findOrFail($id);

        // Obtener solo los usuarios relacionados con esta clínica que son doctores
        $users = $clinica->users()->with('roles')->whereHas('roles', function($query) {
            $query->where('name', 'doctor_admin'); // Filtrar por rol "doctor_admin"
        })->get();

        return view('clinicas.clinica-show', compact('clinica', 'users'));
    }
}
