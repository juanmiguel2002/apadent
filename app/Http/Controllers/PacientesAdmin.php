<?php

namespace App\Http\Controllers;

class PacientesAdmin extends Controller
{
    public function index()
    {

        return view('admin.pacientesAdmin');
    }
}
