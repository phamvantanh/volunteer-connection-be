<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
// use Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\RefreshToken;
use Carbon\Traits\Timestamp;

class AuthController extends Controller
{
  /**
   * Create a new AuthController instance.
   *
   * @return void
   */
  public function __construct()
  {
  }

  /**
   * Get a JWT via given credentials.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function login(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
      'password' => 'required|string|min:6',
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors(), 422);
    }

    if (!$token = auth()->attempt($validator->validated())) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }

    if (!auth()->user()->hasVerifiedEmail()){
      return response()->json(['error' => 'Email not verify'], 400);
    }
    return $this->createNewAccessTokenAndRefreshToken($token);
  }

  /**
   * Register a User.
   *  
   * @return \Illuminate\Http\JsonResponse
   */
  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|string|between:2,100',
      'email' => 'required|string|email|max:100|unique:users',
      'password' => 'required|string|confirmed|min:6',
      'password_confirmation' => 'required',
      'role' => 'required|string',
      'date_of_birth' => 'required|date',
      'phone' => 'required|string'
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors()->toJson(), 422);
    }
    $user = User::create(array_merge(
      $validator->validated(),
      ['password' => Hash::make($request->password)]
    ));
    $user['id']=User::where('email',  $user->email)->first()->id;
    $user->sendEmailVerificationNotification();
    return response()->json([
      'message' => 'User successfully registered',
      'user' => $user
    ], 200);
  }

  /**
   * Log the user out (Invalidate the token).
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function logout(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'refresh_token' => 'required',
    ]);
    if ($validator->fails()) {
      return response()->json($validator->errors(), 422);
    }

    RefreshToken::where([
      'refresh_token' => $request->refresh_token,
    ])->delete();

    if(auth()->check()){
      auth()->logout();
    }

    return response()->json(['message' => 'User successfully signed out'],200);
  }

  /**
   * Refresh a token.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function refresh(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'refresh_token' => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors(), 422);
    }

    return $this->generateTokenAndRefreshToken($request->refresh_token);
  }

  /**
   * Get the authenticated User.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function userProfile()
  {
    return response()->json([
      'user' => auth()->user()
    ], 200);
  }

  /**
   * Get the token array structure.
   *
   * @param  string $token
   *
   * @return \Illuminate\Http\JsonResponse
   */
  protected function generateTokenAndRefreshToken($refresh_token)
  { // when access token expired
    $refreshToken = RefreshToken::where([
      'refresh_token' => $refresh_token,
    ]);
    $row = $refreshToken->first();
    if ($data = $refreshToken->first()) {
      $refreshToken->delete();
      if ($data->expired_at < now()) {
        return response()->json(['message' => 'Unauthorized'], 401);
      }
      $userInfo = User::where('id',  $row->user_id)->first();

      $newToken = auth()->login($userInfo);
      return $this->createNewAccessTokenAndRefreshToken($newToken);
    } else {
      return response()->json(['message' => 'Unauthorized'], 401);
    }
  }

  protected function createNewRefreshToken()
  {
    $valueToken = bin2hex(random_bytes(20));
    $expired_at = date_add(now(), date_interval_create_from_date_string("7 days"));
    $refresh_token = ['user_id' => auth()->user()->id, 'refresh_token' => $valueToken, 'expired_at' =>  $expired_at];
    RefreshToken::create($refresh_token);
    return $valueToken;
  }

  protected function createNewAccessTokenAndRefreshToken($token)
  { //login
    $refresh_token = $this->createNewRefreshToken();
    return response()->json([
      'refresh_token' => $refresh_token,
      'access_token' => $token,
      'token_type' => 'bearer',
      'expires_at' => auth()->factory()->getTTL() * 60,
      'user' => auth()->user()
    ]);
  }

  public function changePassWord(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'old_password' => 'required|string|min:6',
      'new_password' => 'required|string|confirmed|min:6',
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors()->toJson(), 422);
    }
    $userId = auth()->user()->id;

    $user = User::where('id', $userId)->update(
      ['password' =>  Hash::make($request->new_password)]
    );

    return response()->json([
      'message' => 'User successfully changed password',
      'user' => $user,
    ], 200);
  }
}
