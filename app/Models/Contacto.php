<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    use HasFactory;

    protected $table = 'contactos';

    //en base a tabla contactos
    protected $fillable = [
        'tipo_mensaje',
        'rut',
        'nombre',
        'patente_vehiculo',
        'fecha_denuncia',
        'lugar_denunciado',
        'ciudad_origen',
        'ciudad_destino',
        'telefono',
        'ciudad_residencia',
        'mensaje',
    ];

}
