<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasFactory;
    protected $table = 'mensajes';
    protected $fillable = [
        'trat_etapa_id',
        'user_id',
        'message',
    ];

    public function PacienteTrata()
    {
        return $this->belongsTo(PacienteTrat::class, 'trat_etapa_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
