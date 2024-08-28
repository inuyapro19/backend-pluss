<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Acomodacion
 * @package App\Models
 * @version February 20, 2019, 11:18 pm -03
 *
 * @property string descripcion
 */
class Acomodacion extends Model
{
    use SoftDeletes;

    //use HasMediaTrait;

    public $table = 'acomodacions';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'titulo',
        'imagen',
        'descripcion',
        'position',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'titulo'=>'string',
        'descripcion' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'titulo'=>'required',
        'descripcion' => 'required'
    ];

    public function registerMediaCollections()
    {

        $this
            ->addMediaCollection('acomodo')

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
