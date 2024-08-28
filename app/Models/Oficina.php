<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Oficina extends Model
{
   // use SoftDeletes;

    //use HasMediaTrait;

    public $table = 'oficinas';


   // protected $dates = ['deleted_at'];


    public $fillable = [
        'ciudad',
        'tipo',
        'direccion',
        'telefono',
        'imagen',
        'horario_at',
        'mapa',
        'position',
        'status'
    ];


    protected $casts = [
        'ciudad' => 'string',
        'direccion' => 'string',
        'telefono' => 'string',
        'horario_at' => 'string',
        'mapa' => 'string',
        'position'=>'string'
    ];


    public static $rules = [
        'ciudad' => 'required',
        'direccion' => 'required'
    ];


    public static function oficinas_menu()
    {

            return Oficina::orderBy('position','asc')->get();


    }

    public $registerMediaConversionsUsingModelInstance = true;

    public function registerMediaCollections()
    {

        $this
            ->addMediaCollection('oficina')

            // ->singleFile()
            ->registerMediaConversions(function (Media $media = NULL ) {
                $this
                    ->addMediaConversion('small')
                    ->fit('crop', 300, 300);

                $this
                    ->addMediaConversion('medium')
                    ->fit('crop', 500, 500);

                $this
                    ->addMediaConversion('large')
                    ->fit( 'crop',1000, 1000);
            });
    }


}
