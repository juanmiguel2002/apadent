<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Livewire\WithPagination;

class Pacientes extends Controller
{
    //
    use WithPagination;

    public function index(){

        return view('pacientes');
    }
}
