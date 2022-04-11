<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
class comment extends Model
{
    use Notifiable;
    protected $table = "comment";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_blog', 'id_user', 'name_user', 'id_comment', 'comment','image_user'
    ];
}
