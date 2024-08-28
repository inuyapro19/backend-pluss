<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Avisos extends Model
{
    protected $table = 'avisos';

    protected $fillable = ['titulo','descripcion','imagen','position','created_at','status'];

    public function getCreatedAtttribute($value)
    {
        return $value->format('dd-mm-YY');
    }

}
