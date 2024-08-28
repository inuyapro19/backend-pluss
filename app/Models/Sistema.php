<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Sistema
 * @package App\Models
 * @version April 25, 2019, 10:23 pm -03
 *
 * @property string categoria
 * @property string titulo
 * @property string descripcion
 */
class Sistema extends Model
{
    use SoftDeletes;

    public $table = 'sistemas';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'categoria',
        'titulo',
        'descripcion',
        'imagen',
        'status'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'categoria' => 'string',
        'titulo' => 'string',
        'descripcion' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'categoria' => 'required',
        'titulo' => 'required',
        'descripcion' => 'required'
    ];

    public function registerMediaCollections()
    {

        $this
            ->addMediaCollection('sistemas')

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
