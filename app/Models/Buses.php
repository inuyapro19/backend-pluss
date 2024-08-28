<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Buses
 * @package App\Models
 * @version February 20, 2019, 8:07 pm -03
 *
 * @property  titulo
 * @property string descripcion
 * @property string modelo
 * @property integer position
 */
class Buses extends Model
{
    use SoftDeletes;

    //use HasMediaTrait;

    public $table = 'buses';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'titulo',
        'descripcion',
        'imagen',
        'modelo',
        'position',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'descripcion' => 'string',
        'modelo' => 'string',
        'position' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'titulo' => 'required',
        'descripcion' => 'required',
        'modelo' => 'required'
    ];
    public $registerMediaConversionsUsingModelInstance = true;

    public function registerMediaCollections()
    {

        $this
            ->addMediaCollection('buses')

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

    /*public function updateMedia(array $newMediaArray, string $collectionName = 'default')
    {
        $this->removeMediaItemsNotPresentInArray($newMediaArray, $collectionName);

        return [];
    }*/

}
