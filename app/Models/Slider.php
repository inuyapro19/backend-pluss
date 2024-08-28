<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Slider extends Model
{
    //use SoftDeletes;
    //use HasMediaTrait;

  //  use \Rutorika\Sortable\SortableTrait;

    public $table = 'sliders';


    //protected $dates = ['deleted_at'];


    public $fillable = [
        'imagen',
        'titulo',
        'categorias',
        'link',
        'position',
        'status',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'imagen' => 'string',
        'link' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'imagen' => 'required',
    ];
}
