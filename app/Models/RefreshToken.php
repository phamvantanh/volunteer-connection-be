<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefreshToken extends Model
{
  //
  protected $table = 'refresh_token';
  protected $primaryKey = 'user_id';
  protected $fillable = ['user_id', 'refresh_token', 'expired_at'];
  public $timestamps = true;

  protected $casts = [
    'user_id' => 'string',
];

}
