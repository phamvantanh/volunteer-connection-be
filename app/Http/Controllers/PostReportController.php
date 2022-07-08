<?php

namespace App\Http\Controllers;

use App\Models\PostReport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\PostReport\PostReportRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class PostReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $postReportRepo;
    public function __construct(PostReportRepository $postReportRepo)
    {
        $this->postReportRepo = $postReportRepo;
    }

    public function index()
    {
        if ($postReports = $this->postReportRepo->getAll()) {
            foreach ($postReports as $report){
                // $user_name =  $report->user;
                $report['user_name']= $report->user->name;
                // $post_title = $report->postTitle;
                $report['title']= $report->postInfo->title;
                $report['slug']= $report->postInfo->slug;
            }
            return response()->json(['postReports' => $postReports], Response::HTTP_OK);
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
            $params = $request->only(
                'post_id',
                'reason'
            );
            $params['post_id'] = $request->input('post_id');
            $params['reason'] = $request->input('reason');

            if ($this->postReportRepo->create([
                'post_id' => $params['post_id'],
                'reason' =>  $params['reason'],
                'user_id' => auth()->user()->id,
            ])) {
                return response()->json(['postReport' => $this->postReportRepo->getLatestCreate()], Response::HTTP_OK);
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
        if ($postReport = $this->postReportRepo->find($param)) {
            return response()->json(['postReport' => $postReport], Response::HTTP_OK);
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
            if (auth()->user()->role != 'admin') {
                return response()->json(['Message' => "Not permision"], Response::HTTP_FORBIDDEN);
            }
            $infoUpdate = $request->only(
                'decision',
                'is_solved'
            );

            // dd('check');
            $infoUpdate['is_solved'] = $request->input('is_solved');
            $infoUpdate['decision'] = $request->input('decision');

            if ($postReport = $this->postReportRepo->update($id, $infoUpdate)) {
                if( $infoUpdate['decision'] == 'Xóa bài viết'){
                    $report = $this->postReportRepo->find($id);
                    $report->post->delete();
                    return response()->json(['Message' => 'Post deleted! Report is solved'], Response::HTTP_OK);
                } else return  response()->json(['Message' => " Report is solved"], Response::HTTP_OK);
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
            if (auth()->user()->role === 'admin') {
                if ($this->postReportRepo->delete($id)) {
                    return response()->json(['Message' => 'Xóa thành công'], Response::HTTP_OK);
                } else return  response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
            } else return response()->json(['Message' => "Not permision"], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);
        }
    }
}
