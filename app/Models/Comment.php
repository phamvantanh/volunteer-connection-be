<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{    
    public $table = "comment";
    protected $fillable = [
        'id', 'user_id','event_id','content', 'parent_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id')->select('name','url_account','avatar_url');
    }
}
