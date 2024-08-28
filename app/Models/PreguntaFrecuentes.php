<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreguntaFrecuentes extends Model
{
    protected $table = 'pregunta_frecuentes';

    protected $fillable = ['titulo','descripcion','estado'];
}
