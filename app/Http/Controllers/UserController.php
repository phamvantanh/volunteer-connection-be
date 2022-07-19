<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Notification;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $userRepo;
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    // get all post

    public function index()
    {
        if (auth()->user()->role != 'admin') {
            return response()->json(['Message' => "Only admin can create new account"], Response::HTTP_FORBIDDEN);
        }
        if ($users = $this->userRepo->getAll()) {
            return response()->json(['users' => $users], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    // add new user 

    public function store(Request $request)
    {
        if (auth()->user()->role != 'admin') {
            return response()->json(['Message' => "Only admin can create new account"], Response::HTTP_FORBIDDEN);
        }

        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|between:1,100',
                'email' => 'required|string|email|max:100|unique:users',
                'password' => 'required|string|confirmed|min:6',
                'password_confirmation' => 'required',
                'role' => 'required|string',
                'gender' => 'required',
                'avatar_url'=>'required|string:max:500',
                'date_of_birth' => 'required|date',
                'phone' => 'required|string'
              ]);
            //   dd($request->gender);
              if ($validator->fails()) {
                return response()->json([$validator->errors()->toJson(),], 422);
              }
              $user = User::create(array_merge(
                $validator->validated(),
                ['password' => Hash::make($request->password),
                'url_account'=> Str::slug($request->name)."-".rand()
                ]
              ));
              $user['id']=User::where('email',  $user->email)->first()->id;
              $user->sendEmailVerificationNotification();
              return response()->json([
                'message' => 'User successfully registered',
                'user' => $user
              ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'Message' =>  $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**]
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */

    // get user by id or slug

    public function show($param)
    {
        if ($user = $this->userRepo->findByIdOrUrl($param)) {
            return response()->json(['user' => $user], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);

    }

    public function myEvent($param)
    {
        if ($user = $this->userRepo->findByIdOrUrl($param)) {
            $events = $user->event;
            foreach ($events as $event){
                $event->category;
            }
            return response()->json(['events' => $events], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);

    }
    public function myPost($param)
    {
        if ($user = $this->userRepo->findByIdOrUrl($param)) {
            $posts = $user->post;
            return response()->json(['posts' => $posts], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);

    }

    public function myRegisteredEvent($param)
    {
        if ($user = $this->userRepo->findByIdOrUrl($param)) {
            $rows = $user->registeredEvent;
            $events=[];
            foreach ($rows as $row){
                $event=$row->event;
                $event->category;
                $event->user;
                array_push($events, $event);
            }
            return response()->json(['events' => $events], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);

    }

    public function myCertificate($param)
    {
        if ($user = $this->userRepo->findByIdOrUrl($param)) {
            $certificates = $user->certificate;
            return response()->json(['certificates' => $certificates], Response::HTTP_OK);
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
            if (auth()->user()->id == $id || auth()->user()->role == 'admin'){     
                $validator = Validator::make($request->all(), [
                    'name' => 'string|between:1,100',
                    'email' => [
                        'email',
                        Rule::unique('users')->ignore($id)],
                    'avatar_url'=>'nullable',
                    'role' => 'string',
                    'gender' => 'string',
                    'date_of_birth' => 'date',
                    'phone' => 'string|max:20',
                    'about' => 'nullable|string',
                    'url_account' => 'nullable|string'
                  ]);
              
                  if ($validator->fails()) {
                    return response()->json($validator->errors()->toJson(), 422);
                  }

                $user = User::find($id)->first();
                $infoUpdate['name'] = $request->name?$request->name:$user->name;
                $infoUpdate['email'] = $request->email?$request->email:$user->email;
                $infoUpdate['avatar_url']= $request->avatar_url?$request->avatar_url:$user->avatar_url;
                $infoUpdate['role']= $request->role? $request->role:$user->role;
                $infoUpdate['gender']= $request->gender?$request->gender:$user->gender;
                $infoUpdate['date_of_birth']= $request->date_of_birth;
                $infoUpdate['phone']= $request->phone?$request->phone:$user->phone;
                $infoUpdate['about']= $request->about;
                $infoUpdate['url_account']= $request->url_account;

                if ($userUpdated = $this->userRepo->update($id, $infoUpdate)) {
                    // if($user->email != $infoUpdate['email']){
                    //     $userUpdated->unverify();
                    //     $userUpdated->sendEmailVerificationNotification();
                    // }
                    return response()->json(['user' => $this->userRepo->getLatestUpdate()], Response::HTTP_OK);
                } else return  response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
            } else return response()->json(['Message' => "Not permision"], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return response()->json(['Message' => $e], Response::HTTP_NOT_FOUND);
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
            if (auth()->user()->role == 'admin') {
                if ($this->userRepo->delete($id)) {
                    return response()->json(['Message' =>'Delete user successfully'], Response::HTTP_OK);
                } else return  response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
            } else return response()->json(['Message' => "Not permision"], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);
        }
    }

    public function seenNoti($id)
    {
         //
         try {
                $noti=Notification::select('*')->where('id',$id)->first();
                $noti->seenNoti;
                return response()->json(['Message' =>'Đánh dấu đã đọc'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['Message' => 'OK'], Response::HTTP_OK);
        }
    }
}
