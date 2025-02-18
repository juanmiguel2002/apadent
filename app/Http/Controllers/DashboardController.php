<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    public function show(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('admin')){
            // Dashboard for admin
            return view('clinicas.index');
        }elseif ($user->hasRole('doctor') || $user->hasRole('doctor_admin')) {
            // Dashboard for doctor (related to a clinica)
            $clinica = $user->clinica;
            return view('pacientes.index', compact('clinica'));
        }
        elseif ($user->hasRole('clinica')) {
            // Dashboard for clinica
            $pacientes = Paciente::where('clinica_id', $user->clinica_id)->get();

            return view('pacientes.index', compact('pacientes'));
        }

        // Default case (redirect to login)
        return redirect()->route('login');
    }
}
