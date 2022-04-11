<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use Notifiable;
    protected $table = "blog";
    public $timestamp = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'image', 'description', 'content', 'id_auth'
    ];
    protected $appends = array('rating');

    public function comment() {
        return $this->hasMany('App\Models\comment', 'id_blog');
    }

    public function rates() {
        return $this->hasMany('App\Models\Rate_Blog', 'blog_id');
    }

    public function getRatingAttribute() {
        return $this->rates()->avg('rate') ?? 0;
    }
}
