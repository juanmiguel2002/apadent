<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ClinicaUser extends Pivot
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'clinica_user';
}
