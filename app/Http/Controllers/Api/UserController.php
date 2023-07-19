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
}
