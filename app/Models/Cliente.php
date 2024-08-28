<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Cliente extends Model
{


    use SoftDeletes;

    public $table = 'clientes';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'nombre',
        'url',
        'imagen',
        'position',
        'status'
    ];


    protected $casts = [
        'nombre' => 'string',
        'url' => 'string'
    ];


    public static $rules = [
        'nombre' => 'required'
    ];

    public function registerMediaCollections()
    {

        $this
            ->addMediaCollection('clientes')

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
