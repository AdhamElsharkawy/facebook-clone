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
use Illuminate\Support\Facades\Http;
use App\Models\Department;

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
            'remember_me' => 'required|boolean',
        ];
        try {
            $request->validate($rules);
        } catch (\Exception $e) {
            return $this->apiValidationTrait($request->all(), $rules);
        }

        // request auth from odoo, and save user data in db if not exist and update his if exists and incase if user is admin or super_admin he will not request auth from odoo 
        // generate token
        $token = auth('api')->attempt($request->only('email', 'password'));
        if (!$token) {
            $odoo_user = Http::post(env('ODOO_URL') . '/api/external_auth/', [
                'params' => [
                    'db' => env('ODOO_DB'),
                    'login' => $request->email,
                    'password' => $request->password,
                ]
            ]);

            if (isset($odoo_user->json()['error'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => $odoo_user->json()['error']['message'],
                    'error_name' => $odoo_user->json()['error']['data']['name'],
                ], 401);
            }
            $odoo_user = $odoo_user->json()['result'];

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                $form_data["title"] = $odoo_user['job_title'];
                $form_data["name"] = $odoo_user['name'];
                $form_data["email"] = $odoo_user['username'];
                $form_data["password"] = Hash::make($request->password);
                $form_data["image"] = $odoo_user['web.base.url'] . $odoo_user['image'];
                $user = User::create($form_data);

                // check if his odoo department exist in our db and if not create it
                $department = Department::where('name', 'like', '%' . $odoo_user['department'] . '%')->first();
                if (!$department) {
                    $department = Department::create([
                        'name' => $odoo_user['department'],
                    ]);
                }
                $user->department_id = $department->id;
                $user->save();
            } else {
                $user->title = $odoo_user['job_title'];
                $user->name = $odoo_user['name'];
                $user->password = Hash::make($request->password);
                $user->status = 'active';
                $user->save();
            }
            $token = auth('api')->attempt($request->only('email', 'password'));
        }

        if (auth('api')->user()->status == 'blocked') {
            return response()->json([
                'status' => 'error',
                'message' => 'Your account is blocked',
            ], 401);
        }

        // set token expire time
        if ($request->remember_me) {
            auth('api')->factory()->setTTL(60 * 24 * 30);
        } else {
            auth('api')->factory()->setTTL(60 * 8);
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
                'expires_in' => auth('api')->factory()->getTTL() * 60,
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
