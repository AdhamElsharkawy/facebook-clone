<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Http\Traits\SeoTrait;
use App\Models\Seo;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    use GeneralTrait, SeoTrait;

    public function index()
    {
        $users = User::select("id", "name", "email", "image")->get();
        $users->makeHidden(["image"]);
        $seo = Seo::first();
        return $this->apiSuccessResponse(
            ["users" => $users],
            $this->seo('Users', 'home-page', $seo->description, $seo->keywords),
            'Users retreived successfully',
        );
    } //end of index

    public function show($user_id)
    {
        $user = User::select("id", "email")->find($user_id);
        if (!$user) return response()->json(["status" => "error", "message" => "User not found"], 404);

        // generate response
        $response = [
            'posts' => ProfileController::getPosts($user->id),
            'user' => ProfileController::getProfileData($user->email),
        ];

        $seo = Seo::first();
        return $this->apiSuccessResponse(
            $response,
            $this->seo('User', 'home-page', $seo->description, $seo->keywords),
            'User retreived successfully',
        );
    } //end of show
}
