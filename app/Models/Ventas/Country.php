<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $connection = 'ticket_mysql';
    protected $table = 'countries';
    protected $fillable = [
        'name',
        'code',
    ];


}
