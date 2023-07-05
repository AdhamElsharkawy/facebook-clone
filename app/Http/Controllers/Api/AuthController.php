<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Http\Traits\SeoTrait;
use App\Models\Seo;

class AuthController extends Controller
{

    use GeneralTrait, SeoTrait;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    } // end of __construct

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        $token = auth('api')->attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = User::where('email', $request->email)
            ->select("id", "title", "name", "email", "image", "mobile", "status", "birth_date", "score", "social_links", "front_theme", "department_id")
            ->with(['department:id,name'])
            ->with([
                'experiences' => function ($query) {
                    $query->select("title", "type", "start_date", "end_date", "is_current", "user_id", "company_id")
                        ->with(['company' => function ($query) {
                            $query->select('id', 'name', 'image');
                        }]);
                },
            ])
            ->with(['educations' => function ($query) {
                $query->select("degree", "major", "start_date", "end_date", "is_current", "user_id", "location", "college_id")
                    ->with(['college' => function ($query) {
                        $query->select('id', 'name', 'image');
                    }]);
            }])
            ->with(['certifications' => function ($query) {
                $query->select("major", "location", "start_date", "end_date", "is_current", "valid_until", "confirmation_link", "user_id", "college_id")
                    ->with(['college' => function ($query) {
                        $query->select('id', 'name', 'image');
                    }]);
            }])
            ->first();


        $response = [
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

    public function register(Request $request)
    {
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

        $token = auth('api')->login($user);
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

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }
}
