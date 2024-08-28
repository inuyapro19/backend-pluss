<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgenciaCookie extends Model
{
    use HasFactory;

    protected $connection = 'ticket_mysql';

    protected $table = 'agencia_cookies';

    protected $fillable = [
        'name',
        'valor',
        'access_token'
    ];


}
