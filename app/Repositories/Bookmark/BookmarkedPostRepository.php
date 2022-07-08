<?php

namespace App\Repositories\Bookmark;

use App\Models\BookmarkedPost;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class BookmarkedPostRepository extends BaseRepository implements BookmarkRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\BookmarkedPost::class;
    }

    public function getLatestCreate()
    {
        return BookmarkedPost::latest()->first();
    }

    public function getLatestUpdate()
    {
        return BookmarkedPost::latest('updated_at')->first();
    }

    public function getByUserAndPostId($user_id,$post_id){
        return BookmarkedPost::select('*')
        ->where('user_id',$user_id)
        ->where('post_id',$post_id)
        ->first();
    }

    public function deleteRow($user_id,$post_id)
    {
        $result = BookmarkedPost::select('*')
        ->where('user_id',$user_id)
        ->where('post_id',$post_id);
        if ($result) {
            $result->delete();
            return true;
        }
        return false;
    }

    

}
