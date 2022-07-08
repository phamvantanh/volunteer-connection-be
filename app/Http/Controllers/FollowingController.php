<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Repositories\Following\FollowingRepository;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Following;



class FollowingController extends Controller
{  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $followingRepo;
    public function __construct(FollowingRepository $followingRepo)
    {
        $this->followingRepo = $followingRepo;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {
        if (auth()->user()->role != 'volunteer') {
            return response()->json(['Message' => "Not permision for your account"], Response::HTTP_FORBIDDEN);
        }
        try {
            $params = $request->only(
                'organization_id'
            );
            $params['organization_id'] = $request->input('organization_id');

            if($this->followingRepo->checkFollow(auth()->user()->id,$params['organization_id']))
            {
                return response()->json(['Message' => 'Đã follow!'], Response::HTTP_BAD_REQUEST);
            }
            if(auth()->user()->id==$params['organization_id'])
            {
                return response()->json(['Message' => 'Không thể theo dõi bản thân!'], Response::HTTP_BAD_REQUEST);
            }
            $OrganizaionInfo = User::where('id',  $params['organization_id'])->first();
            if($OrganizaionInfo->role != 'organization'){
                return response()->json(['Message' => 'Chỉ có thể theo dõi tổ chức!'], Response::HTTP_BAD_REQUEST);
            }

            if ($this->followingRepo->create([
                'organization_id' => $params['organization_id'],
                'volunteer_id' => auth()->user()->id,
            ])) {
                return response()->json(['Message' => "Follow Success"], Response::HTTP_OK);
            } else return response()->json(['Message' => 'Đã xảy ra lỗi. Thử lại sau'], Response::HTTP_BAD_REQUEST);
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


    public function getFollowingOfVolunteer($id)
    {
        if ($listOrganization =  Following::where('volunteer_id',  $id)->get()) {
            foreach ($listOrganization as $org) {
                $org->Organization;
            }
            return response()->json(['Organization' => $listOrganization], Response::HTTP_OK);
        } else return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);

    }

    public function getFollowingOfOrganization($id)
    {
        if ($listVolunteer = Following::where('organization_id',  $id)->get()) {
            foreach ($listVolunteer as $volunteer) {
                $volunteer->Volunteer;
            }
            return response()->json(['Volunteer' => $listVolunteer], Response::HTTP_OK);
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

    public function destroy($id)
    {
         //
         try {
             if(auth()->user()->role =='volunteer'){
                if ($this->followingRepo->deleteRow(auth()->user()->id,$id)) {
                    return response()->json(['Message' => 'Delete Success'], Response::HTTP_OK);
                } else return  response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
             }
             else if(auth()->user()->role =='organization'){
                if ($this->followingRepo->deleteRow($id,auth()->user()->id)) {
                    return response()->json(['Message' => 'Delete Success'], Response::HTTP_OK);
                } else return  response()->json(['Message' => Config::get('constants.RESPONSE.400')], Response::HTTP_BAD_REQUEST);
             }    
        } catch (\Exception $e) {
            return response()->json(['Message' => Config::get('constants.RESPONSE.404')], Response::HTTP_NOT_FOUND);
        }
    }
}
