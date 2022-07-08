<?php

namespace App\Models;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\SoftDeletes; // add soft delete
use App\Models\RegisteredVolunteer;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title', 'category_id','content','slug','deadline','status','is_published','user_id','event_thumbnail'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $casts = [
        'deadline' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->select('name','url_account');
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->select('name');
    }

    public function registerList()
    {
        return $this->hasMany(RegisteredVolunteer::class);
    }

}
