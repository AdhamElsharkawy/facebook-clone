<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\College;
use App\Http\Traits\SeoTrait;
use App\Http\Traits\GeneralTrait;
use App\Models\Seo;

class CollegeController extends Controller
{
    use GeneralTrait, SeoTrait;

    public function index()
    {
        $colleges = College::select('id', 'name')->get();
        $colleges->makeHidden(['image_path']);
        $seo = Seo::first();
        return $this->apiSuccessResponse(
            [
                'colleges' => $colleges,
            ],
            $this->seo('Colleges', 'colleges', $seo->description, $seo->keywords),
            "Colleges retreived successfully."
        );
    }
}
