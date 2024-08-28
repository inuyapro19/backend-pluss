<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminMenuItem extends Model
{
    use HasFactory;

    protected $table = 'admin_menu_items';

    protected $fillable = [
        'label',
        'link',
        'menu',
        'sort',
    ];

}
