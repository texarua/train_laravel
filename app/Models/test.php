<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
class test extends Model
{
    use Notifiable;

    protected $table = "test";
    public $timestamp = true;


     protected $fillable = [
        'title', 'image', 'description', 'content'
    ];
}
