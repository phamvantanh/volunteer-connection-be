<?php

namespace App\Repositories\Event;

use App\Models\Event;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class EventRepository extends BaseRepository implements EventRepositoryInterface
{
    public function getModel()
    {
        return \App\Models\Event::class;
    }
    public function getLatestCreate()
    {
        return Event::latest()->first();
    }

    public function getLatestUpdate()
    {
        return Event::latest('updated_at')->first();
    }

    public function getUserCreated($id)
    {
        return event::where('id', $id)->first()->user_id;
            
    }

    public function findByIdOrSlug($param)
    {
        return event::where('id', $param)
        ->orWhere('slug', $param)
        ->firstOrFail();
    }

    public function searchByTitleOrContent($param){
        return Event::select('*')->where('is_published',1)
        ->where(function ($query) use ($param) {
            $query->where('title', 'like', '%'.$param.'%')
                ->orwhere('content', 'like', '%'.$param.'%');
        })
        ->get();
    
    }

    public function findEventbyCategory($param){
        return $event = Event::select('*')
         ->join('category', 'category.id', '=', 'events.category_id')
         ->where('category.id', $param)
         ->get();
     }
}
