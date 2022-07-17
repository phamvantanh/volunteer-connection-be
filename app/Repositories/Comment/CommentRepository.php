<?php

namespace App\Repositories\Comment;

use App\Models\Comment;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\Comment::class;
    }
    public function getLatestCreate()
    {
        return Comment::latest()->first();
    }

    public function getLatestUpdate()
    {
        return Comment::latest('updated_at')->first();
    }
    // public function getUserCreated($id)
    // {
    //     return post::where('id', $id)->first()->user_id;
            
    // }

    // public function findByIdOrSlug($param)
    // {
    //     return post::where('id', $param)
    //     ->orWhere('slug', $param)
    //     ->firstOrFail();
    // }

    // public function searchByTitleOrContent($param){
    //     return post::query()
    //     ->where('title', 'like', '%'.$param.'%')
    //     ->orwhere('content', 'like', '%'.$param.'%')
    //     ->get();
    
    // }
}
