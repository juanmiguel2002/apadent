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
            return view('clinicas');
        }elseif ($user->hasRole('doctor')) {
            // Dashboard for doctor (related to a clinica)
            $clinica = $user->clinica;
            return view('pacientes', compact('clinica'));
        }
        elseif ($user->hasRole('doctor_admin')) {
            // Dashboard for doctor (related to a clinica)
            $clinica = $user->clinica;
            return view('pacientes', compact('clinica'));
        }
        elseif ($user->hasRole('clinica_user')) {
            // Dashboard for clinica
            $pacientes = Paciente::where('clinica_id', $user->clinica_id)->get();

            return view('pacientes', compact('pacientes'));
        }

        // Default case (redirect to login)
        return redirect()->route('login');
    }
}
