<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'locale'
    ];


       protected $table = 'settings';

    //desactivar el primary key por defecto



    protected $primaryKey = 'key';

    //desactivar el autoincremento

    public $incrementing = false;

    public $timestamps = false;


}
