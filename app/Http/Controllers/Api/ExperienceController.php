<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\SeoTrait;
use App\Http\Traits\GeneralTrait;
use App\Models\Seo;
use App\Models\Company;

class ExperienceController extends Controller
{
    use GeneralTrait, SeoTrait;

    public function store(Request $request)
    {
        $validations = $this->apiValidationTrait($request->all(), [
            "title" => "required|string",
            "description" => "nullable|string",
            "type" => "required|integer|in:1,2,3",
            "start_date" => "required|date",
            "end_date" => "nullable|date|after_or_equal:start_date",
            "is_current" => "nullable|integer|in:0,1",
            "company_id" => "nullable|integer|exists:companies,id",
            "company_name" => "nullable|string",
        ]);
        if ($validations) return $validations;

        if ($request->company_id) {
            $experience = auth('api')->user()->experiences()->create($request->all());
        } else {
            $company = Company::firstOrCreate([
                'name' => $request->company_name,
            ]);
            $formData = $request->except('company_name');
            $formData['company_id'] = $company->id;
            $experience = auth('api')->user()->experiences()->create($formData);
        }

        $seo = Seo::first();
        return $this->apiSuccessResponse(
            [
                'experience' => $experience->load('company'),
            ],
            $this->seo('Experience', 'experience', $seo->description, $seo->keywords),
            "Experience created successfully."
        );
    } //end of store

    public function update(Request $request, $id)
    {
        $validations = $this->apiValidationTrait($request->all(), [
            "title" => "required|string",
            "description" => "nullable|string",
            "type" => "required|integer|in:1,2,3",
            "start_date" => "required|date",
            "end_date" => "nullable|date|after_or_equal:start_date",
            "is_current" => "nullable|integer|in:0,1",
            "company_id" => "nullable|integer|exists:companies,id",
            "company_name" => "nullable|string",
        ]);
        if ($validations) return $validations;

        $experience = auth('api')->user()->experiences()->find($id);
        if (!$experience) return response()->json(['error' => 'Experience not found.'], 404);

        if ($request->company_id) {
            $experience->update($request->all());
        } else {
            $company = Company::firstOrCreate([
                'name' => $request->company_name,
            ]);
            $formData = $request->except('company_name');
            $formData['company_id'] = $company->id;
            $experience->update($formData);
        }

        $seo = Seo::first();
        return $this->apiSuccessResponse(
            [
                'experience' => $experience->load('company'),
            ],
            $this->seo('Experience', 'experience', $seo->description, $seo->keywords),
            "Experience updated successfully."
        );
    } //end of update

    public function destroy($id)
    {
        $experience = auth('api')->user()->experiences()->find($id);
        if (!$experience) return response()->json(['error' => 'Experience not found.'], 404);

        $experience->delete();
        return $this->apiSuccessResponse(
            [],
            $this->seo('Experience', 'experience'),
            "Experience deleted successfully."
        );
    } //end of destroy
}
