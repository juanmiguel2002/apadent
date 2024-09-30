<?php

namespace App\Http\Controllers;


class UsersClinica extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }
}
