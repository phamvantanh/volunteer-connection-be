<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{    
    public $table = "review";
    protected $fillable = [
        'id', 'user_id','event_id','content','rating'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $casts = [
        'rating' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id')->select('name','url_account','avatar_url');
    }
}
