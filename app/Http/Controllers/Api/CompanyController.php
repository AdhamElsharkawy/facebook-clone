<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Http\Traits\SeoTrait;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Seo;

class CompanyController extends Controller
{
    use GeneralTrait, SeoTrait;

    public function index()
    {
        $companies = Company::select('id', 'name')->get();
        $companies->makeHidden(['image_path']);
        $seo = Seo::first();
        return $this->apiSuccessResponse(
            [
                'companies' => $companies,
            ],
            $this->seo('Companies', 'companies', $seo->description, $seo->keywords),
            "Companies retreived successfully."
        );
    } //end of index
}
