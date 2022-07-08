<?php

namespace App\Models;
use App\Models\User;
use App\Models\PostReport;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title', 'content','post_thumbnail','slug','user_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id')->select(['id', 'name','url_account']);
    }

    public function report() {
        return $this->hasMany(PostReport::class);
    }
}
