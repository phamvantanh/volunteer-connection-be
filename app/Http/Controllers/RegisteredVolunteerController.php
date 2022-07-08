<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\RegisteredVolunteer\RegisteredVolunteerRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Category;


class RegisteredVolunteerController extends Controller
{  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $registeredVolunteerRepo;
    public function __construct(RegisteredVolunteerRepository $registeredVolunteerRepo)
    {
        $this->registeredVolunteerRepo = $registeredVolunteerRepo;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {
        try {
            $params = $request->only(
                'event_id'
            );
            $params['event_id'] = $request->input('event_id');

            if($this->registeredVolunteerRepo->getByUserAndEventId(auth()->user()->id,$params['event_id']))
            {
                return response()->json(['Message' => 'Registered before!'], Response::HTTP_BAD_REQUEST);
            }

            if ($this->registeredVolunteerRepo->create([
                'event_id' => $params['event_id'],
                'user_id' => auth()->user()->id,
            ])) {
                return response()->json(['Message' => "Success"], Response::HTTP_OK);
            } else return response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json([
                'Message' =>  $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    
    public function update(Request $request)
    {
        try {
            // if (auth()->user()->role != 'admin') {
            //     return response()->json(['Message' => "Not permision"], Response::HTTP_FORBIDDEN);
            // }
            $infoUpdate = $request->only(
                'user_id',
                'event_id',
                'is_confirmed'

            );
            // $params['event_id'] = $request->input('event_id');
            // $params['user_id'] = $request->input('user_id');
            // $params['is_confirmed'] = $request->input('is_confirmed');

            //  dd($params['event_id'], $params['user_id'], $params['is_confirmed']);
            $infoUpdate['user_id'] = $request->input('user_id');
            $infoUpdate['event_id'] = $request->input('event_id');
            $infoUpdate['is_confirmed'] = $request->input('is_confirmed');
            // dd($infoUpdate);
            if ($this->registeredVolunteerRepo->updateInfo($infoUpdate)) {
                    return response()->json(['Message' => 'success'], Response::HTTP_OK);
            } else return  response()->json(['Message' => '400'], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['Message' => $e], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */


    // public function getBookmarkedEventsOfUsers()
    // {
    //     if ($listEvent = auth()->user()->bookmarkedEvent) {
    //         foreach($listEvent as $event){
    //             $event->event;
    //             $event->event['user'] = User::select('name','url_account')
    //             ->where('id', $event->user_id)
    //             ->get();
    //             $event->event['category'] = Category::select('name')
    //             ->where('id', $event->event_id)
    //             ->get();
    //         }
    //         return response()->json(['bookmarkedEvent' => $listEvent], Response::HTTP_OK);
    //     } else return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);

    // }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */

     // delete

    public function destroy($event_id)
    {
        try {
                if ($this->registeredVolunteerRepo->deleteRow(auth()->user()->id,$event_id)) {
                    return response()->json(['Message' => 'Delete Success'], Response::HTTP_OK);
                } else return  response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);
        }
    }


    public function removeVolunteer(Request $request)
    {
        try {
            $params = $request->only(
                'event_id',
                'user_id'
            );
            $params['event_id'] = $request->input('event_id');
            $params['user_id'] = $request->input('user_id');
            // dd($request->input('event_id'),$request->input('user_id'));
            if ($this->registeredVolunteerRepo->deleteRow($params['user_id'],$params['event_id'])) {
                return response()->json(['Message' => 'Delete Success'], Response::HTTP_OK);
            } else return  response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json([
                'Message' =>  $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
