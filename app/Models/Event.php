<?php

namespace App\Models;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\SoftDeletes; // add soft delete
use App\Models\RegisteredVolunteer;
use App\Models\Comment;
use App\Models\Review;

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

    public function comment()
    {
        return $this->hasMany(Comment::class)->where('parent_id','=', null)->orderBy('created_at', 'DESC');
    }

    public function review()
    {
        return $this->hasMany(Review::class)->orderBy('created_at', 'DESC');
    }

}
