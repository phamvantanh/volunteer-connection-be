<?php

namespace App\Repositories\Bookmark;

use App\Models\BookmarkedEvent;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class BookmarkedEventRepository extends BaseRepository implements BookmarkRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\BookmarkedEvent::class;
    }
    
    public function getLatestCreate()
    {
        return BookmarkedEvent::latest()->first();
    }

    public function getLatestUpdate()
    {
        return BookmarkedEvent::latest('updated_at')->first();
    }

    public function getByUserAndEventId($user_id,$event_id){
        return BookmarkedEvent::select('*')
        ->where('user_id',$user_id)
        ->where('event_id',$event_id)
        ->first();
    }

    public function deleteRow($user_id,$event_id)
    {   
        $result = BookmarkedEvent::select('*')
        ->where('user_id',$user_id)
        ->where('event_id',$event_id);
        if ($result) {
            $result->delete();
            return true;
        }
        return false;
    }
    

}
