<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Post\PostRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


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

    public function index()
    {
        if ($posts = $this->postRepo->getAll()) {
            return response()->json(['posts' => $posts], Response::HTTP_OK);
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
    public function show($param)
    {
        if ($post = $this->postRepo->findByIdOrSlug($param)) {
            return response()->json(['post' => $post], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);

    }

    public function searchByTitleOrContent($param)
    {   
        if ($post = $this->postRepo->searchByTitleOrContent($param)) {
            return response()->json(['post' => $post], Response::HTTP_OK);
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
    public function destroy($id)
    {
         //
         try {
            if (auth()->user()->id == $this->postRepo->getUserCreated($id)) {
                if ($this->postRepo->delete($id)) {
                    return response()->json(['Message' => Config::get('constants.RESPONSE.200')], Response::HTTP_OK);
                } else return  response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
            } else return response()->json(['Message' => "Not permision"], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);
        }
    }
}
