<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{    
    public $table = "certificate";
    protected $fillable = [
        'event_id', 'user_id','name','organization_name', 'issue_date', 'url', 'is_published'
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
}
