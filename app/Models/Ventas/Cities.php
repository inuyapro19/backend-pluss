<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    use HasFactory;

    protected $connection = 'ticket_mysql';

   protected $table = 'cities';

   protected $fillable = [
       'id',
        'url_name' ,
        'name',
        'latitude',
        'longitude',
        'url' ,
        'country_id'
   ];

   public $timestamps = false;

}
