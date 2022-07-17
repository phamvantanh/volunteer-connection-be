<?php

namespace App\Repositories\Review;

use App\Models\Review;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class ReviewRepository extends BaseRepository implements ReviewRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\Review::class;
    }
    public function getLatestCreate()
    {
        return Review::latest()->first();
    }

    public function getLatestUpdate()
    {
        return Review::latest('updated_at')->first();
    }

}
