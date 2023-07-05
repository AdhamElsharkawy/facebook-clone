<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Traits\GeneralTrait;
use App\Http\Traits\SeoTrait;
use App\Models\Seo;

class AuthController extends Controller
{

    use GeneralTrait, SeoTrait;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => 'login']);
    } // end of __construct

    public function login(LoginRequest $request)
    {
        // generate token
        $token = auth('api')->attempt($request->only('email', 'password'));
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }
        // get user data
        $user = UserController::getProfileData($request->email);
        // generate response
        $response = [
            'posts' => UserController::getPosts($user->id),
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ];
        $seo = Seo::first();
        return $this->apiSuccessResponse(
            $response,
            $this->seo('login', 'login', $seo->description, $seo->keywords),
            'Login successfully'
        );
    } // end of login

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    } // end of logout

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    } // end of refresh
}
