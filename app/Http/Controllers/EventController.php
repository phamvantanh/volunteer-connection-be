<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Event\EventRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\BookmarkedEvent;


class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $eventRepo;
    public function __construct(EventRepository $eventRepo)
    {
        $this->eventRepo = $eventRepo;
    }

    public function index()
    {
        if ($events = $this->eventRepo->getAll()) {
            foreach ($events as $event){
                $event->user;
                $event->category;
            }
            return response()->json(['events' => $events], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (auth()->user()->role != 'organization') {
            return response()->json(['Message' => "Not permision for your account"], Response::HTTP_FORBIDDEN);
        }

        try {
            $params = $request->only(
                'title',
                'category_id',
                'content',
                'deadline',
                'status',
                'is_published',
                'event_thumbnail'
            );
            $params['title'] = $request->input('title');
            $params['category_id'] = $request->input('category_id');
            $params['content'] = $request->input('content');
            $params['deadline'] = $request->input('deadline');
            $params['status'] = $request->input('status');
            $params['is_published'] = $request->input('is_published');
            $params['event_thumbnail'] = $request->input('event_thumbnail');


            if ($this->eventRepo->create([
                'title' => $params['title'],
                'category_id' => $params['category_id'],
                'content' =>  $params['content'],
                'deadline' => $params['deadline'],
                'status' => $params['status'],
                'is_published' => $params['is_published'],
                'event_thumbnail'=> $params['event_thumbnail'],
                'slug'=> Str::slug($params['title'])."-".rand(),
                'user_id' => auth()->user()->id,
            ])) {
                return response()->json(['event' => $this->eventRepo->getLatestCreate()], Response::HTTP_OK);
            } else return response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json([
                'Message' =>  $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($param)
    {
        if ($event = $this->eventRepo->findByIdOrSlug($param)) {
            $event->user;
            $event->category;
            $event->registerList;
            return response()->json(['event' => $event], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);

    }

    public function searchByTitleOrContent($param)
    {   
        if ($events = $this->eventRepo->searchByTitleOrContent($param)) {
            foreach ($events as $event){
                $event->user;
                $event->category;
                $user_id = auth()->user()?auth()->user()->id:null;
                $event['bookmark'] = BookmarkedEvent::select('*')
                                    ->where('user_id',$user_id)
                                    ->where('event_id',$event->id)
                                    ->count()!==0?true:false;
            }
            return response()->json(['events' => $events], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);

    }

    public function getEventofCategory($param)
    {
        if ($event = $this->eventRepo->findEventbyCategory($param)) {
            return response()->json(['events' => $event], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);

    }

    public function getRegisterListOfEvent($param)
    {
        if ($event = $this->eventRepo->findByIdOrSlug($param)) {
            $lists = $event->registerList;
            foreach ($lists as $list){
                $list->user;
            }
            return response()->json(['user_list' => $event->registerList], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            if (auth()->user()->id != $this->eventRepo->getUserCreated($id)) {
                return response()->json(['Message' => "Not permision"], Response::HTTP_FORBIDDEN);
            }

            $infoUpdate = $request->only(
                'title',
                'category_id',
                'content',
                'deadline',
                'status',
                'is_published'
            );

            $infoUpdate['title'] = $request->input('title');
            $infoUpdate['title'] = $request->input('title');
            $infoUpdate['content'] = $request->input('content');
            $infoUpdate['deadline'] = $request->input('deadline');
            $infoUpdate['status'] = $request->input('status');
            $infoUpdate['is_published'] = $request->input('is_published');
            $infoUpdate['slug']= Str::slug($request->input('title'))."-".rand();

            if ($event = $this->eventRepo->update($id, $infoUpdate)) {
                return response()->json(['event' => $this->eventRepo->getLatestUpdate()], Response::HTTP_OK);
            } else return  response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);
        }
    }


    public function updatePublish(Request $request, $id)
    {
        try {
            if (auth()->user()->id != $this->eventRepo->getUserCreated($id)) {
                return response()->json(['Message' => "Not permision"], Response::HTTP_FORBIDDEN);
            }

            $infoUpdate = $request->only(
                'is_published'
            );

            $infoUpdate['is_published'] = $request->input('is_published');

            if ($event = $this->eventRepo->update($id, $infoUpdate)) {
                return response()->json(['event' => $this->eventRepo->getLatestUpdate()], Response::HTTP_OK);
            } else return  response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         //
         try {
            if (auth()->user()->id == $this->eventRepo->getUserCreated($id) || auth()->user()->role == 'admin') {
                if ($event = $this->eventRepo->delete($id)) {
                    return response()->json(['Message' => "delete success"], Response::HTTP_OK);
                } else return  response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
            } else return response()->json(['Message' => "Not permision"], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);
        }
    }
}
