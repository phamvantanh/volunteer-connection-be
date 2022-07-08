<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookmarkedEvent extends Model
{   
    public $table = "bookmarked_event";
    protected $fillable = [
        'user_id','event_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
