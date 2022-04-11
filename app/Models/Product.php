<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model
{
    
    protected $table = "product";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_category', 'id_brand','id_user', 'image','name', 'web_id', 'price','status','sale', 'condition', 'detail', 'company_profile'
    ];

    public function Brand()
    {
        return $this->hasOne('App\Models\Brand', 'id','id_brand');
    }
    public function Category()
    {
        return $this->hasOne('App\Models\Category', 'id','id_category');
    }

    public static function scopeSearch($query, $searchTerm)
    {
        return $query->where('name', 'like', '%' .$searchTerm. '%')
                     ->orWhere('web_id', 'like', '%' .$searchTerm. '%');
    }

}
