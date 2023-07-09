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
        $events = \App\Models\Event::search($search)->get();
        $users = \App\Models\User::search($search)->get();
        $posts = \App\Models\Post::search($search)->get();
        $polls = \App\Models\Poll::search($search)->get();
        $experiences = \App\Models\Experience::search($search)->get();
        $companies = \App\Models\Company::search($search)->get();
        $colleges = \App\Models\College::search($search)->get();
        $departments = \App\Models\Department::search($search)->get();
        $certifications = \App\Models\Certification::search($search)->get();
        $educations = \App\Models\Education::search($search)->get();

        $response_data = [
            'events' => $events,
            'users' => $users,
            'posts' => $posts,
            'polls' => $polls,
            'experiences' => $experiences,
            'companies' => $companies,
            'colleges' => $colleges,
            'departments' => $departments,
            'certifications' => $certifications,
            'educations' => $educations,
        ]; //end of response_data

        $seo = Seo::first();
        return $this->apiSuccessResponse($response_data, $this->Seo($seo->title, 'search', $seo->description, $seo->keywords));
    } //end of __invoke
}
