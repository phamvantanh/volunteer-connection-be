<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Certificate\CertificateRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Event;


class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $certificateRepo;
    public function __construct(CertificateRepository $certificateRepo)
    {
        $this->certificateRepo = $certificateRepo;
    }

    public function index()
    {
        if ($certificates = $this->certificateRepo->getAll()) {
            return response()->json(['certificates' => $certificates], Response::HTTP_OK);
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
                'name' => 'required|string|between:1,255',
                'organization_name'=> 'required|string|between:1,255',
                'issue_date' => 'required|date',
                'event_id' => 'nullable',
                'user_id' => 'required',
                'url' =>   'required',
              ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 422);
            }
            $params['name'] = $request->name;
            $params['organization_name'] = $request->organization_name;
            $params['issue_date'] = $request->issue_date;
            $params['event_id'] = $request->event_id;
            $params['user_id'] = $request->user_id;
            $params['url'] = $request->url;

            if ($this->certificateRepo->create([
                'name' => $params['name'],
                'organization_name' => $params['organization_name'],
                'issue_date' => $params['issue_date'],
                'event_id' =>  $params['event_id'],
                'user_id' =>   $params['user_id'],
                'url' =>   $params['url'],
                'is_published' => 1
            ])) {
                return response()->json(['certificate' => $this->certificateRepo->getLatestCreate()], Response::HTTP_OK);
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
        if ($certificate = $this->certificateRepo->find($param)) {
            return response()->json(['certificate' => $certificate], Response::HTTP_OK);
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
                    'name' => 'required|string|between:1,255',
                    'organization_name'=> 'required|string|between:1,255',
                    'issue_date' => 'required|date',
                    'event_id' => 'nullable',
                    'user_id' => 'required',
                    'url' =>   'required',
                    'is_published' => 'required'
                  ]);           

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 422);
            }

            $infoUpdate['is_published'] =$request->is_published;
            $infoUpdate['name'] = $request->name;
            $infoUpdate['organization_name'] = $request->organization_name;
            $infoUpdate['issue_date'] = $request->issue_date;
            $infoUpdate['event_id'] = $request->event_id;
            $infoUpdate['user_id'] = $request->user_id;
            $infoUpdate['url'] = $request->url;
            if ($category = $this->certificateRepo->update($id, $infoUpdate)) {
                return response()->json(['certificate' => $this->certificateRepo->getLatestUpdate()], Response::HTTP_OK);
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
                if ($this->certificateRepo->delete($id)) {
                    return response()->json(['Message' => "Delete success"], Response::HTTP_OK);
                } else return  response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);
        }
    }
}
