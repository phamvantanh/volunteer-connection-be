<?php

namespace App\Models;
use App\Models\User;
use App\Models\Event;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{    
    public $table = "notifications";
    protected $fillable = [
        'id', 'user_id','event_id','content', 'is_seen','source_user_id'
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
    public function event()
    {
        return $this->belongsTo(Event::class,'event_id')->select('title','slug');
    }

    public function seenNoti() {
        $this->is_seen = 1;
        $this->save();
    }
}
