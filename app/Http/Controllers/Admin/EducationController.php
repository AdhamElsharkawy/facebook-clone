<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\College;
use App\Models\Education;
use App\Http\Requests\Admin\Education\StoreEducationRequest;
use App\Http\Requests\Admin\Education\UpdateEducationRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $educations = Education::with('college','user')->latest()->paginate(10);
        $colleges = College::all();
        $users = User::all();
        return ['educations' => $educations,'colleges' => $colleges,'users' => $users];
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEducationRequest $request)
    {
        $form_data = $request->validated();
        $form_data['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d H:i:s');
        $form_data['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d H:i:s');


        Education::create($form_data);
        return response()->json(['message' => __('Education Created Successfully')]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Education $education)
    {
        return response()->json(['education' => $education]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEducationRequest $request, Education $education)
    {
        $form_data = $request->validated();
        $form_data['start_date'] = Carbon::parse($request->start_date);
        $form_data['end_date'] = Carbon::parse($request->end_date);
        $education->update($form_data);

        return response()->json(['message' => __('Education Updated Successfully')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Education $education)
    {
        $education->delete();
        return response()->json(['message' => __('Education Deleted Successfully')]);
    }

    public function destroyAll(Request $request)
    {
        $educations = Education::whereIn('id', $request->educations)->get();
        foreach ($educations as $education) {
            $education->delete();
        }
        return response()->json(['message' => __('Events Deleted Successfully')]);
    } //end of destroyAll//

}
