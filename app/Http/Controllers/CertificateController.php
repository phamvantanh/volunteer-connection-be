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
              ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 422);
            }
            $params['name'] = $request->name;

            if ($this->categoryRepo->create([
                'name' => $params['name'],
            ])) {
                return response()->json(['category' => $this->categoryRepo->getLatestCreate()], Response::HTTP_OK);
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
            if (auth()->user()->role != 'admin') {
                return response()->json(['Message' => "Not permision for your account"], Response::HTTP_FORBIDDEN);
            }
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|between:1,255|unique:category',
              ]);

            if ($validator->fails()) {
                return response()->json($validator->errors()->toJson(), 422);
            }

            $infoUpdate['name'] =$request->name;
            if ($category = $this->categoryRepo->update($id, $infoUpdate)) {
                return response()->json(['category' => $this->categoryRepo->getLatestUpdate()], Response::HTTP_OK);
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
