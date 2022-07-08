<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Notifications\VerifyEmail;
use App\Models\Post;
use App\Models\BookmarkedPost;
use App\Models\BookmarkedEvent;
use App\Models\Following;


class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable;
    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','role','phone','date_of_birth','url_account','gender','about','is_disable','avatar_url'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'id' => 'string',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
        
    /**
 * Send the email verification notification.
 *
 * @return void
 */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail); // my notification
    }

    public function post() {
        return $this->hasMany(Post::class);
    }

    public function bookmarkedPost() {
        return $this->hasMany(BookmarkedPost::class);
    }

    public function bookmarkedEvent() {
        return $this->hasMany(BookmarkedEvent::class);
    }

    public function Organization() {
        return $this->hasMany(Following::class,'organization_id');
    }

    public function Volunteer() {
        return $this->hasMany(Following::class,'volunteer_id');
    }

    public function unverify() {
        $this->email_verified_at = null;

        $this->save();
    }
}
