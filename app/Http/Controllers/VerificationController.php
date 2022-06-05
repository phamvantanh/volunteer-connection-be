<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VerificationController extends Controller
{
    use VerifiesEmails;

    /**
     * Show the email verification notice.
     *
     */
    public function show()
    {
        //
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {

        $user = User::findOrFail($request->route('id'));
        Auth::login($user);
        if ($request->route('id') == $user->getKey() &&
            $user->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }
        return response()->json('Email verified!');
//        return redirect($this->redirectPath());
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
          ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::where([
            'email' => $request->email,
          ])->first();

        if($user==null){
            return response()->json('Email incorrect', 401);
        }
        if ($user->hasVerifiedEmail()) {
            return response()->json('User already have verified email!', 422);
        }
        $user->sendEmailVerificationNotification();

        return response()->json('The notification has been resubmitted',200);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }
}
