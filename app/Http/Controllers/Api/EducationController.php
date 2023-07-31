<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\SeoTrait;
use App\Http\Traits\GeneralTrait;
use App\Models\College;
use App\Models\Seo;

class EducationController extends Controller
{
    use GeneralTrait, SeoTrait;

    public function store(Request $request)
    {
        $validations = $this->apiValidationTrait($request->all(), [
            "degree" => "required|string",
            "major" => "required|string",
            "start_date" => "required|date",
            "end_date" => "nullable|date|after_or_equal:start_date",
            "is_current" => "nullable|integer|in:0,1",
            "location" => "nullable|string",
            "college_id" => "nullable|integer|exists:colleges,id",
            "college_name" => "nullable|string",
        ]);
        if ($validations) return $validations;

        if ($request->college_id) {
            $education = auth('api')->user()->educations()->create($request->all());
        } else {
            $college = College::firstOrCreate([
                'name' => $request->college_name,
            ]);
            $formData = $request->except('college_name');
            $formData['college_id'] = $college->id;
            $education = auth('api')->user()->educations()->create($formData);
        }

        $seo = Seo::first();
        return $this->apiSuccessResponse(
            [
                'education' => $education->load('college'),
            ],
            $this->seo('Education', 'education', $seo->description, $seo->keywords),
            "Education created successfully."
        );
    } //end of store

    public function update(Request $request, $id)
    {
        $validations = $this->apiValidationTrait($request->all(), [
            "degree" => "required|string",
            "major" => "required|string",
            "start_date" => "required|date",
            "end_date" => "nullable|date|after_or_equal:start_date",
            "is_current" => "nullable|integer|in:0,1",
            "location" => "nullable|string",
            "college_id" => "nullable|integer|exists:colleges,id",
            "college_name" => "nullable|string",
        ]);
        if ($validations) return $validations;

        $education = auth('api')->user()->educations()->find($id);
        if (!$education) return $this->apiErrorResponse("Education not found.");

        if ($request->college_id) {
            $education->update($request->all());
        } else {
            $college = College::firstOrCreate([
                'name' => $request->college_name,
            ]);
            $formData = $request->except('college_name');
            $formData['college_id'] = $college->id;
            $education->update($formData);
        }

        $seo = Seo::first();
        return $this->apiSuccessResponse(
            [
                'education' => $education->load('college'),
            ],
            $this->seo('Education', 'education', $seo->description, $seo->keywords),
            "Education updated successfully."
        );
    } //end of update

    public function destroy($id)
    {
        $education = auth('api')->user()->educations()->find($id);
        if (!$education) return $this->apiErrorResponse("Education not found.");

        $education->delete();
        return $this->apiSuccessResponse(
            [],
            $this->seo('Education', 'education'),
            "Education deleted successfully."
        );
    } //end of destroy
}
