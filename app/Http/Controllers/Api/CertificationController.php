<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\SeoTrait;
use App\Http\Traits\GeneralTrait;
use App\Models\College;
use App\Models\Seo;

class CertificationController extends Controller
{
    use GeneralTrait, SeoTrait;

    public function store(Request $request)
    {
        $validations = $this->apiValidationTrait($request->all(), [
            "major" => "required|string",
            "start_date" => "required|date",
            "end_date" => "nullable|date|after_or_equal:start_date",
            "is_current" => "nullable|integer|in:0,1",
            "location" => "nullable|string",
            "valid_until" => "nullable|date",
            "confirmation_link" => "nullable|string",
            "college_id" => "nullable|integer|exists:colleges,id",
            "college_name" => "nullable|string",
        ]);
        if ($validations) return $validations;

        if ($request->college_id) {
            $certification = auth('api')->user()->certifications()->create($request->all());
        } else {
            $college = College::firstOrCreate([
                'name' => $request->college_name,
            ]);
            $formData = $request->except('college_name');
            $formData['college_id'] = $college->id;
            $certification = auth('api')->user()->certifications()->create($formData);
        }

        $seo = Seo::first();
        return $this->apiSuccessResponse(
            [
                'certification' => $certification->load('college'),
            ],
            $this->seo('Certification', 'certification', $seo->description, $seo->keywords),
            "Certification created successfully."
        );
    } //end of store

    public function update(Request $request, $id)
    {
        $validations = $this->apiValidationTrait($request->all(), [
            "major" => "required|string",
            "start_date" => "required|date",
            "end_date" => "nullable|date|after_or_equal:start_date",
            "is_current" => "nullable|integer|in:0,1",
            "location" => "nullable|string",
            "valid_until" => "nullable|date",
            "confirmation_link" => "nullable|string",
            "college_id" => "nullable|integer|exists:colleges,id",
            "college_name" => "nullable|string",
        ]);
        if ($validations) return $validations;

        $certification = auth('api')->user()->certifications()->find($id);
        if (!$certification) return $this->apiErrorResponse("Certification not found.");

        if ($request->college_id) {
            $certification->update($request->all());
        } else {
            $college = College::firstOrCreate([
                'name' => $request->college_name,
            ]);
            $formData = $request->except('college_name');
            $formData['college_id'] = $college->id;
            $certification->update($formData);
        }

        $seo = Seo::first();
        return $this->apiSuccessResponse(
            [
                'certification' => $certification->load('college'),
            ],
            $this->seo('Certification', 'certification', $seo->description, $seo->keywords),
            "Certification updated successfully."
        );
    } //end of update

    public function destroy($id)
    {
        $certification = auth('api')->user()->certifications()->find($id);
        if (!$certification) return $this->apiErrorResponse("Certification not found.");

        $certification->delete();
        return $this->apiSuccessResponse([], "Certification deleted successfully.");
    } //end of destroy
}
