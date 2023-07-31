<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Company;
use App\Models\Experience;
use App\Http\Requests\Admin\Experience\StoreExperienceRequest;
use App\Http\Requests\Admin\Experience\UpdateExperienceRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExperienceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $experiences = Experience::with('company','user')->latest()->paginate(10);
        $companies = Company::all();
        $users = User::all();
        return ['experiences' => $experiences,'companies' => $companies,'users' => $users];
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExperienceRequest $request)
    {
        $form_data = $request->validated();
        $form_data['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d H:i:s');
        $form_data['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d H:i:s');


        Experience::create($form_data);
        return response()->json(['message' => __('Experience Created Successfully')]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Experience $experience)
    {
        return response()->json(['experience' => $experience]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExperienceRequest $request, Experience $experience)
    {
        $form_data = $request->validated();
        $form_data['start_date'] = Carbon::parse($request->start_date);
        $form_data['end_date'] = Carbon::parse($request->end_date);
        $experience->update($form_data);

        return response()->json(['message' => __('Experience Updated Successfully')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Experience $experience)
    {
        $experience->delete();
        return response()->json(['message' => __('Experience Deleted Successfully')]);
    }

    public function destroyAll(Request $request)
    {
        $experiences = Experience::whereIn('id', $request->experiences)->get();
        foreach ($experiences as $experience) {
            $experience->delete();
        }
        return response()->json(['message' => __('Experience Deleted Successfully')]);
    } //end of destroyAll//


}
