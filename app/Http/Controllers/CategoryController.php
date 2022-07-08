<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Category\CategoryRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Event;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $categoryRepo;
    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function index()
    {
        if ($categories = $this->categoryRepo->getAll()) {
            foreach($categories as $category){
                $numberEvent = Event::where('category_id',$category->id)->count();
                $category['numberEvent'] = $numberEvent;
            }
            return response()->json(['categories' => $categories], Response::HTTP_OK);
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
        if (auth()->user()->role != 'admin') {
            return response()->json(['Message' => "Not permision for your account"], Response::HTTP_FORBIDDEN);
        }

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|between:1,255|unique:category',
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

    public function getEventofCategory($param)
    { 
        $category = $this->categoryRepo->find($param);
        if ($event = $category->event) {
            return response()->json(['category_name'=>$category->name,'events' => $event], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($param)
    {
        if (auth()->user()->role != 'admin') {
            return response()->json(['Message' => "Not permision for your account"], Response::HTTP_FORBIDDEN);
        }
        if ($category = $this->categoryRepo->find($param)) {
            return response()->json(['category' => $category], Response::HTTP_OK);
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
            if (auth()->user()->role == 'admin') {
                if ($this->categoryRepo->delete($id)) {
                    return response()->json(['Message' => "Delete success"], Response::HTTP_OK);
                } else return  response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
            } else return response()->json(['Message' => "Not permision"], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);
        }
    }
}
