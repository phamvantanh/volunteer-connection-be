<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Following extends Model
{   
    public $table = "following_relationship";
    protected $fillable = [
        'volunteer_id','organization_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    public function Organization() {
        return $this->belongsTo(User::class,'organization_id');
    }

    public function Volunteer() {
        return $this->belongsTo(User::class,'volunteer_id');
    }

}
