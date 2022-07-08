<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Event;
use App\Models\Post;
use App\Models\PostReport;



class AdminController extends Controller
{  

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */


    public function getDashBoardInfo()
    {
        if (auth()->user()->role != 'admin') {
            return response()->json(['Message' => "Not permision for your account"], Response::HTTP_FORBIDDEN);
        }
        try {
            $numberVolunteer = User::where('role','volunteer')->count();
            $numberOrganization = User::where('role','organization')->count();
            $numberPost = Post::count();
            $numberEvent = Event::count();
            $numberReportNotSolved = PostReport::where('is_solved',0)->count();

            return response()->json([
                'numberVolunteer' => $numberVolunteer,
                'numberOrganization'=> $numberOrganization,
                'numberPost' => $numberPost,
                'numberEvent' => $numberEvent,
                'numberReportNotSolved' => $numberReportNotSolved
            ], Response::HTTP_OK);   

        } catch (\Exception $e) {
            return response()->json([
                'Message' =>  $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function getUserList()
    {
        if (auth()->user()->role != 'admin') {
            return response()->json(['Message' => "Not permision for your account"], Response::HTTP_FORBIDDEN);
        }
        try {
            $users = User::all();
            return response()->json(['users' =>  $users], Response::HTTP_OK);   

        } catch (\Exception $e) {
            return response()->json([
                'Message' =>  $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

   
}
