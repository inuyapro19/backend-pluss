<?php

namespace App\Models\Ventas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;
    protected $connection = 'ticket_mysql';
    protected $table = 'order_details';

    protected $fillable = [
        'order_id',
        'detalles'
    ];

    public $timestamps = false;

}
