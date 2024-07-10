<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    use HasFactory;
    protected $table = 'mensajes';
    protected $fillable = ['message'];

    public function tratEtapa()
    {
        return $this->belongsTo(TratEtapa::class, 'trat_etapa_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
