<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookmarkedPost extends Model
{   
    public $table = "bookmarked_post";
    protected $fillable = [
        'user_id','post_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
