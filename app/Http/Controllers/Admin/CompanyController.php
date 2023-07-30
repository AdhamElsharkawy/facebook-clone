<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Traits\ImageTrait;
use App\Models\Company;
use App\Http\Requests\Admin\Company\StoreCompanyRequest;
use App\Http\Requests\Admin\Company\UpdateCompanyRequest;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    use ImageTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::latest()->paginate(5);
        return ['companies' => $companies];
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompanyRequest $request)
    {
        $form_data = $request->validated();
        //image uploading
        $request->image ? $form_data['image'] = $this->uploadS3Image($request->image, 'images/companies') : '';

        Company::create($form_data);

        return response()->json(['message' => __('Company Created Successfully')]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        return response()->json(['company' => $company]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $form_data = $request->validated();
        //image uploading
        if ($request->image) {
            $company->image !=  'assets/images/default.png' ? $this->deleteS3Image($company->image) : '';
            $form_data['image'] = $this->uploadS3Image($request->image, 'images/companies');
        } else {
            $form_data['image'] = $company->image;
        }
        $company->update($form_data);

        return response()->json(['message' => __('Company Updated Successfully')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        $company->image != 'assets/images/default.png' ? $this->deleteS3Image($company->image) : '';
        $company->delete();

        return response()->json(['message' => __('Company Deleted Successfully')], 200);
    }

    public function destroyAll(Request $request)
    {
        $companies = Company::whereIn('id', $request->companies)->get();
        foreach ($companies as $company) {
            $company->image != 'assets/images/default.png' ? $this->deleteS3Image($company->image) : '';
            $company->delete();
        }
        return response()->json(['message' => __('Companies Deleted Successfully')]);
    } //end of destroyAll
}
