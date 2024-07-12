<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Pacientes extends Controller
{
    //

    public function index(){

        return view('pacientes');
    }
}
