<?php

namespace App\Models;
use App\Models\User;
use App\Models\Post;

use Illuminate\Database\Eloquent\Model;

class PostReport extends Model
{
    protected $fillable = [
        'post_id', 'reason','is_solved','slug','user_id','decision'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public function user()
    {
        return $this->belongsTo(User::class)->select('name');
    }

    public function postInfo()
    {
        return $this->belongsTo(Post::class,'post_id')->select('title','slug');

    }
    public function post()
    {
        return $this->belongsTo(Post::class);
    }


}
