<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Department;
use App\Http\Requests\Admin\Department\StoreDepartmentRequest;
use App\Http\Requests\Admin\Department\UpdateDepartmentRequest;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $departments = Department::latest()->get();
        return ['departments' => $departments];
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDepartmentRequest $request)
    {
        //encrypt password
        $form_data = $request->validated();

        Department::create($form_data);

        return response()->json(['message' => __('Department Created Successfully')]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Department $department)
    {
        return response()->json(['department' => $department]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department)
    {
        $form_data = $request->validated();
        $department->update($form_data);
        return response()->json(['message' => __('Department Updated Successfully')]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Department $department)
    {
        $department->delete();
        return response()->json(['message' => __('Department Deleted Successfully')], 200);
    }

    public function destroyAll(Request $request)
    {
        $departments = Department::whereIn('id', $request->department)->get();
        foreach ($departments as $department) {
            $department->delete();
        }
        return response()->json(['message' => __('All Departments Deleted Successfully')]);
    } //end of destroyAll



}
