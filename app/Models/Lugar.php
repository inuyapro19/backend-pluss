<?php

namespace App\Models;

use App\Models\Destinos;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Lugar extends Model
{
    //use HasMediaTrait;
    use SoftDeletes;

  protected $table = 'lugares';

  protected $dates = ['deleted_at'];


  protected  $fillable = [
      'destinos_id',
      'nombre_lugar',
      'descripcion',
      'imagen',
      'texto_imagen',
        'status'
  ];


    protected $casts = [
        'destinos_id',
        'nombre_lugar',
        'descripcion',
        'imagen',
        'texto_imagen'
    ];


    public static $rules = [
        'destinos_id'=>'required',
        'nombre_lugar'=>'required',
        'descripcion'=>'required',
        'texto_imagen'=>'required'
    ];

    public function destino(){
        return $this->belongsTo(Destinos::class);
    }

    public $registerMediaConversionsUsingModelInstance = true;

    public function registerMediaCollections()
    {

        $this
            ->addMediaCollection('lugares')

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
