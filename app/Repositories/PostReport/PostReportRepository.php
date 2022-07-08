<?php

namespace App\Repositories\PostReport;

use App\Models\PostReport;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class PostReportRepository extends BaseRepository implements PostReportRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\PostReport::class;
    }
    
    public function getLatestCreate()
    {
        return PostReport::latest()->first();
    }

    public function getLatestUpdate()
    {
        return PostReport::latest('updated_at')->first();
    }

    public function getUserCreated($id)
    {
        return PostReport::where('id', $id)->first()->user_id;
            
    }

}
