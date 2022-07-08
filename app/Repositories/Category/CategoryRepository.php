<?php

namespace App\Repositories\Category;

use App\Models\Category;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\Category::class;
    }
    public function getLatestCreate()
    {
        return Category::latest()->first();
    }

    public function getLatestUpdate()
    {
        return Category::latest('updated_at')->first();
    }
    
    public function getUserCreated($id)
    {
        return Category::where('id', $id)->first()->user_id;
            
    }
}
