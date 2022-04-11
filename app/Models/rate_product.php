<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class rate_product extends Model
{
    protected $table = "rate_product";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_product', 'id_user','rate'
    ];
}
