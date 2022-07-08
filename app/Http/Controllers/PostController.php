<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Post\PostRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\BookmarkedPost;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $postRepo;
    public function __construct(PostRepository $postRepo)
    {
        $this->postRepo = $postRepo;
    }

    // get all post

    public function index()
    {
        if ($posts = $this->postRepo->getAll()) {
            foreach ($posts as $post) {
                $post->user->name;
            }
            return response()->json(['posts' => $posts], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    // add new post 

    public function store(Request $request)
    {
        if (auth()->user()->role != 'organization') {
            return response()->json(['Message' => "Not permision for your account"], Response::HTTP_FORBIDDEN);
        }

        try {
            $params = $request->only(
                'title',
                'content',
                'post_thumbnail'
            );
            $params['title'] = $request->input('title');
            $params['content'] = $request->input('content');
            $params['post_thumbnail'] = $request->input('post_thumbnail');

            if ($this->postRepo->create([
                'title' => $params['title'],
                'content' =>  $params['content'],
                'post_thumbnail' => $params['post_thumbnail'],
                'slug'=> Str::slug($params['title'])."-".rand(),
                'user_id' => auth()->user()->id,
            ])) {
                return response()->json(['post' => $this->postRepo->getLatestCreate()], Response::HTTP_OK);
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

    // get post by id or slug

    public function show($param)
    {
        if ($post = $this->postRepo->findByIdOrSlug($param)) {
            $post->user;
            return response()->json(['post' => $post], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);

    }

    // search post

    public function searchByTitleOrContent($param)
    {   
        if ($posts = $this->postRepo->searchByTitleOrContent($param)) {
            foreach ($posts as $post) {
                $post->user;
                $user_id = auth()->user()?auth()->user()->id:null;
                $post['bookmark'] = BookmarkedPost::select('*')
                                    ->where('user_id',$user_id)
                                    ->where('post_id',$post->id)
                                    ->count()!==0?true:false;
            }
            return response()->json(['posts' => $posts], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    // edit post

    public function update(Request $request, $id)
    {
        try {
            if (auth()->user()->id != $this->postRepo->getUserCreated($id)) {
                return response()->json(['Message' => "Not permision"], Response::HTTP_FORBIDDEN);
            }
            $infoUpdate = $request->only(
                'title',
                'content',
                'post_thumbnail'
            );

            // dd('check');
            $infoUpdate['title'] = $request->input('title');
            $infoUpdate['content'] = $request->input('content');
            $infoUpdate['post_thumbnail'] = $request->input('post_thumbnail');
            $infoUpdate['slug']= Str::slug($request->input('title'))."-".rand();

            if ($post = $this->postRepo->update($id, $infoUpdate)) {
                return response()->json(['post' => $this->postRepo->getLatestUpdate()], Response::HTTP_OK);
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

     // delete

    public function destroy($id)
    {
         //
         try {
            if (auth()->user()->id == $this->postRepo->getUserCreated($id) || auth()->user()->role == 'admin') {
                if ($this->postRepo->delete($id)) {
                    return response()->json(['Message' => Config::get('constants.RESPONSE.200')], Response::HTTP_OK);
                } else return  response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
            } else return response()->json(['Message' => "Not permision"], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);
        }
    }
}
