<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    protected $connection = 'ticket_mysql';
    protected $table = 'clientes';

    protected $fillable = [
        'order_id',
        'tipo_documento',
        'documento',
        'nombre',
        'apellidos',
        'email',
        'telefono',
        'formulario',
    ];

    public $timestamps=false;

}
