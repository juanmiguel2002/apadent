<?php

namespace App\Models;

use App\Casts\DateFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carpeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'carpeta_id',
        'clinica_id'
    ];

    public function parent()
    {
        return $this->belongsTo(Carpeta::class, 'carpeta_id');
    }

    public function carpetasHija(){
        return $this->hasMany(Carpeta::class, 'carpeta_id');
    }

    public function archivos() {
        return $this->hasMany(Archivo::class);
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class, 'carpeta_id');
    }
    public function clinica()
    {
        return $this->belongsTo(Clinica::class, 'clinica_id');
    }

    protected $casts = [
        'created_at' => DateFormat::class,
    ];
}
