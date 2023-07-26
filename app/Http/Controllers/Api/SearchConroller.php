<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Seo;
use Illuminate\Http\Request;
use App\Http\Traits\SeoTrait;

class SearchConroller extends Controller
{
    use GeneralTrait, SeoTrait;
    public function __invoke(Request $request)
    {
        $validate = $this->apiValidationTrait($request->all(), [
            'search' => 'required|string',
        ]);
        if ($validate) {
            return $validate;
        } //end of validate

        $search = $request->search;
        $users = \App\Models\User::search($search)->get();
        $posts = \App\Models\Post::search($search)->get();

        $response_data = [
            'users' => $users,
            'posts' => $posts,
        ]; //end of response_data

        $seo = Seo::first();
        return $this->apiSuccessResponse($response_data, $this->Seo($seo->title, 'search', $seo->description, $seo->keywords));
    } //end of __invoke
}
