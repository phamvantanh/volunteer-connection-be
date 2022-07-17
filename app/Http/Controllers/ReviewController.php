<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Review\ReviewRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Event;


class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $reviewRepo;
    public function __construct(ReviewRepository $reviewRepo)
    {
        $this->reviewRepo = $reviewRepo;
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
                'rating' => 'required',
              ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 422);
            }
            $params['event_id'] = $request->event_id;
            $params['content'] = $request->content;
            $params['rating'] = $request->rating;
            if ($this->reviewRepo->create([
                'event_id' => $params['event_id'],
                'content' => $params['content'],
                'rating' => $params['rating'],
                'user_id' => auth()->user()->id 
            ])) {
                return response()->json(['review' => $this->reviewRepo->getLatestCreate()], Response::HTTP_OK);
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
                'rating' => 'required'          
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 422);
            }

            $infoUpdate['content'] =$request->content;
            $infoUpdate['rating'] =$request->rating;

            if ($review = $this->reviewRepo->update($id, $infoUpdate)) {
                return response()->json(['review' => $this->reviewRepo->getLatestUpdate()], Response::HTTP_OK);
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
                if ($this->reviewRepo->delete($id)) {
                    return response()->json(['Message' => "Delete success"], Response::HTTP_OK);
                } else return  response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);
        }
    }
}
