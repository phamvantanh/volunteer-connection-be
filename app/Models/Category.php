<?php

namespace App\Models;
use App\Models\User;
use App\Models\Event;


use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $table = "category";
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public function event()
    {
        return $this->hasMany(Event::class);
    }

}
