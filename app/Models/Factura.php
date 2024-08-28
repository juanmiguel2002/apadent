<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;
    protected $fillable = ['name','clinica_id', 'user_id', 'ruta'];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function clinica()
    {
        return $this->belongsTo(Clinica::class, 'clinica_id');
    }
}
