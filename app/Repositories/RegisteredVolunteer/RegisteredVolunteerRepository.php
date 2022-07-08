<?php

namespace App\Repositories\RegisteredVolunteer;

use App\Models\RegisteredVolunteer;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class RegisteredVolunteerRepository extends BaseRepository implements RegisteredVolunteerRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\RegisteredVolunteer::class;
    }

    public function getLatestCreate()
    {
        return RegisteredVolunteer::latest()->first();
    }

    public function getLatestUpdate()
    {
        return RegisteredVolunteer::latest('updated_at')->first();
    }

    public function getByUserAndEventId($user_id,$event_id){
        return RegisteredVolunteer::select('*')
        ->where('user_id',$user_id)
        ->where('event_id',$event_id)
        ->first();
    }

    public function deleteRow($user_id,$event_id)
    {
        $result = RegisteredVolunteer::select('*')
        ->where('user_id',$user_id)
        ->where('event_id',$event_id);
        if ($result) {
            $result->delete();
            return true;
        }
        return false;
    }

    public function updateInfo($data)
    {   
        // dd($data['user_id']);
        $result = RegisteredVolunteer::select('*')
        ->where('user_id',$data['user_id'])
        ->where('event_id',$data['event_id']);
        if ($result) {
            $result->update($data);
            return true;
        }
        return false;
    }
    

}
