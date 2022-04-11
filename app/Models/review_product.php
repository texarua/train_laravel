<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class review_product extends Model
{
    protected $table = "review_product";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_product', 'id_user','name_user', 'review','id_sub','avatar_user'
    ];
}
