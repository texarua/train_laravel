<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestApi extends Model
{
    //
    protected $table = "testapi";
    protected $fillable = ['title', 'description', 'price', 'availability'];
}
