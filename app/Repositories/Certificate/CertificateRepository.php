<?php

namespace App\Repositories\Certificate;

use App\Models\Certificate;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class CertificateRepository extends BaseRepository implements CertificateRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\Certificate::class;
    }
    public function getLatestCreate()
    {
        return Certificate::latest()->first();
    }

    public function getLatestUpdate()
    {
        return Certificate::latest('updated_at')->first();
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
