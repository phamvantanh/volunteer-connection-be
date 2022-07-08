<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Bookmark\BookmarkedPostRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class BookmarkedPostController extends Controller
{  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $bookmarkedPostRepo;
    public function __construct(BookmarkedPostRepository $bookmarkedPostRepo)
    {
        $this->bookmarkedPostRepo = $bookmarkedPostRepo;
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
                'post_id'
            );
            $params['post_id'] = $request->input('post_id');

            if($this->bookmarkedPostRepo->getByUserAndPostId(auth()->user()->id,$params['post_id']))
            {
                return response()->json(['Message' => 'Bookmarked before!'], Response::HTTP_BAD_REQUEST);
            }

            if ($this->bookmarkedPostRepo->create([
                'post_id' => $params['post_id'],
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */


    public function getBookmarkedPostsOfUsers()
    {
        if ($listPost = auth()->user()->bookmarkedPost) {
            foreach($listPost as $post){
                $post->post;
                $post->post['user'] = User::select('name','url_account')
                ->where('id', $post->user_id)
                ->get();
            }
            return response()->json(['bookmarkedPost' => $listPost], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);

    }


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

    public function destroy($post_id)
    {
         //
         try {
                if ($this->bookmarkedPostRepo->deleteRow(auth()->user()->id,$post_id)) {
                    return response()->json(['Message' => 'Delete Success'], Response::HTTP_OK);
                } else return  response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);
        }
    }
}
