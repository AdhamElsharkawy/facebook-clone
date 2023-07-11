<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Http\Traits\SeoTrait;
use App\Models\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    use GeneralTrait, SeoTrait;

    public function __construct()
    {
        $this->middleware('jwt:api', ['except' => 'login']);
    } // end of __construct

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|string',
            'remember_me' => 'boolean',
        ];
        try {
            $request->validate($rules);
        } catch (\Exception $e) {
            return $this->apiValidationTrait($request->all(), $rules);
        }
        // generate token
        $token = auth('api')->attempt($request->only('email', 'password'));
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }
        // get user data
        $user = ProfileController::getProfileData($request->email);
        // generate response
        $response = [
            'posts' => ProfileController::getPosts($user->id),
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


    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = auth('api')::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }


}
