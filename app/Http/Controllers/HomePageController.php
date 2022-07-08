<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Post;
use App\Models\Event;
use App\Models\BookmarkedPost;
use App\Models\BookmarkedEvent;
use Illuminate\Support\Facades\DB;



class HomePageController extends Controller
{  

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */


    public function getPostList()
    {
        try {
            $posts =  Post::paginate(6);
            foreach($posts as $post){
                $post->user;
                $user_id = auth()->user()?auth()->user()->id:null;
                $post['bookmark'] = BookmarkedPost::select('*')
                                    ->where('user_id',$user_id)
                                    ->where('post_id',$post->id)
                                    ->count()!==0?true:false;
            }
            return response()->json([
                'post' => $posts,
            ], Response::HTTP_OK);   

        } catch (\Exception $e) {
            return response()->json([
                'Message' =>  $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getEventList()
    {
        try {
            $events =  Event::select('*')->where('is_published',1)->paginate(6);
            foreach($events as $event){
                $event->user;
                $event->category;
                $user_id = auth()->user()?auth()->user()->id:null;
                $event['bookmark'] = BookmarkedEvent::select('*')
                                    ->where('user_id',$user_id)
                                    ->where('event_id',$event->id)
                                    ->count()!==0?true:false;
            }
            return response()->json([
                'event' => $events,
            ], Response::HTTP_OK);   

        } catch (\Exception $e) {
            return response()->json([
                'Message' =>  $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

   
}
