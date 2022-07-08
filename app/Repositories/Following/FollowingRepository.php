<?php

namespace App\Repositories\Following;

use App\Models\Following;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class FollowingRepository extends BaseRepository implements FollowingRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\Following::class;
    }
    
    // public function getLatestCreate()
    // {
    //     return BookmarkedEvent::latest()->first();
    // }

    // public function getLatestUpdate()
    // {
    //     return BookmarkedEvent::latest('updated_at')->first();
    // }

    public function checkFollow($volunteer_id,$organization_id){
        return Following::select('*')
        ->where('volunteer_id',$volunteer_id)
        ->where('organization_id',$organization_id)
        ->first();
    }

    public function deleteRow($volunteer_id,$organization_id)
    {   
        $result = Following::select('*')
        ->where('volunteer_id',$volunteer_id)
        ->where('organization_id',$organization_id);
        if ($result) {
            $result->delete();
            return true;
        }
        return false;
    }
    

}
