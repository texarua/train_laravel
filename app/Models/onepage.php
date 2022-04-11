<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class onepage extends Model
{
    use Notifiable;
    protected $table = "one_page";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug', 'description', 'content', 'id_auth'
    ];
}
