<?php

namespace App\Http\Controllers\Pacientes;

use App\Http\Controllers\Controller;


class Pacientes extends Controller
{
    //
    public function index() {

        return view('pacientes.index');
    }
}
