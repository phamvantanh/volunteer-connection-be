<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\User::class;
    }
    
    public function getLatestCreate()
    {
        return User::latest()->first();
    }

    public function getLatestUpdate()
    {
        return User::latest('updated_at')->first();
    }

    public function findByIdOrUrl($param)
    {
        return User::where('id', $param)
        ->orWhere('url_account', $param)
        ->firstOrFail();
    }

}
