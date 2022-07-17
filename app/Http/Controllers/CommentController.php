<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Comment\CommentRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Event;


class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $commentRepo;
    public function __construct(CommentRepository $commentRepo)
    {
        $this->commentRepo = $commentRepo;
    }

    public function index()
    {
        if ($comments = $this->commentRepo->getAll()) {
            return response()->json(['comments' => $comments], Response::HTTP_OK);
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
        try {
            $validator = Validator::make($request->all(), [
                'event_id' => 'required',
                'content'=> 'required|string|between:1,255',
                'parent_id'
              ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 422);
            }
            $params['event_id'] = $request->event_id;
            $params['content'] = $request->content;
            $params['parent_id'] = $request->parent_id?$request->parent_id:null;
            if ($this->commentRepo->create([
                'event_id' => $params['event_id'],
                'content' => $params['content'],
                'parent_id' => $params['parent_id'],
                'user_id' => auth()->user()->id 
            ])) {
                return response()->json(['comment' => $this->commentRepo->getLatestCreate()], Response::HTTP_OK);
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
        if ($comment = $this->commentRepo->find($param)) {
            return response()->json(['comment' => $comment], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);

    }

    public function fetchComments($param)
    {
        if ($event = $this->eventRepo->findByIdOrSlug($param)) {
            $listComment = $event->comment;
            foreach ($listComment as $comment){
                $comment['replies']=Comment::select('*')->where('parent_id',$comment->id)->get();
            }
            return response()->json(['comments' => $listComment], Response::HTTP_OK);
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
 
            $validator = Validator::make($request->all(), [
                'content'=> 'required|string|between:1,255',              
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 422);
            }

            $infoUpdate['content'] =$request->content;
            if ($comment = $this->commentRepo->update($id, $infoUpdate)) {
                return response()->json(['comment' => $this->commentRepo->getLatestUpdate()], Response::HTTP_OK);
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
                if ($this->commentRepo->delete($id)) {
                    Comment::where('parent_id', $id)->delete();
                    return response()->json(['Message' => "Delete success"], Response::HTTP_OK);
                } else return  response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);
        }
    }
}
