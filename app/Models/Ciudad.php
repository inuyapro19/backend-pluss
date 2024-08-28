<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Ciudad
 * @package App\Models
 * @version January 15, 2019, 2:28 pm -03
 *
 * @property string nombre
 */
class Ciudad extends Model
{
    use SoftDeletes;

    public $table = 'ciudads';
    

    protected $dates = ['deleted_at'];


    public $fillable = [
        'nombre'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'nombre' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'nombre' => 'required'
    ];


    public static function getciudad(){
        return Ciudad::pluck('nombre','id');
    }

    
}
