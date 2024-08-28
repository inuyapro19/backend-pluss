<?php

namespace App\Models;

use App\Models\Lugar;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Destinos extends Model
{
    use SoftDeletes;



    public $table = 'destinos';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'nombre_ciudad',
        'slug',
        'descripcion',
        'imagen_destino',
        'imagen',
        'texto_imagen',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'descripcion' => 'string',
        'texto_imagen' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'nombre_ciudad'=>'required',
        'descripcion' => 'required',
        'texto_imagen' => 'required'
    ];

    //public static function getdestinos()
    //{
       // return  Destinos::pluck('ciudad_id','id');
    //}

    public static function getdestinos(){
            return Destinos::pluck('nombre_ciudad','id');
    }

    /*public function ciudad()
    {
        return  $this->belongsTo(Ciudad::class);
    }*/

    public function lugares()
    {
        return $this->hasMany(Lugar::class);
    }

    public $registerMediaConversionsUsingModelInstance = true;

    public function registerMediaCollections()
    {
        $this
            ->addMediaCollection( 'destino_principal')
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

        $this
            ->addMediaCollection( 'destinos')
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

    public function updateMedia(array $newMediaArray, string $collectionName = 'default')
    {
        $this->removeMediaItemsNotPresentInArray($newMediaArray, $collectionName);

        return [];
    }

}
