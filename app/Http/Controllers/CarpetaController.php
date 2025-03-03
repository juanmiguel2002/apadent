<?php

namespace App\Http\Controllers;

use App\Models\Carpeta;

class CarpetaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.carpeta.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $carpeta = Carpeta::findOrFail($id);

        return view('admin.carpeta.show', compact( 'carpeta', 'id'));
    }

}
