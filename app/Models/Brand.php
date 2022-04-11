<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = "brand";
    public function Product()
    {
        return $this->hasMany('App\Models\Product', 'id_brand');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'brand'
    ];
}
