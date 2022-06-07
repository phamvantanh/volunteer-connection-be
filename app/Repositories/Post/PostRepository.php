<?php

namespace App\Repositories\Post;

use App\Models\Post;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\Post::class;
    }
    public function getLatestCreate()
    {
        return post::latest()->first();
    }

    public function getLatestUpdate()
    {
        return post::latest('updated_at')->first();
    }
    public function getUserCreated($id)
    {
        return post::where('id', $id)->first()->user_id;
            
    }

    public function findByIdOrSlug($param)
    {
        return post::where('id', $param)
        ->orWhere('slug', $param)
        ->firstOrFail();
    }

    public function searchByTitleOrContent($param){
        return post::query()
        ->where('title', 'like', '%'.$param.'%')
        ->orwhere('content', 'like', '%'.$param.'%')
        ->get();
    
    }
}
