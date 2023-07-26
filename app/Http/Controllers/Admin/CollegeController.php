<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Traits\ImageTrait;
use App\Models\College;
use App\Http\Requests\Admin\College\StoreCollegeRequest;
use App\Http\Requests\Admin\College\UpdateCollegeRequest;
use Illuminate\Http\Request;

class CollegeController extends Controller
{

    use ImageTrait;

    public function index()
    {
        $colleges = College::latest()->get();
        return ['colleges' => $colleges];
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCollegeRequest $request)
    {
        $form_data = $request->validated();
        //image uploading
        $request->image ? $form_data['image'] = $this->uploadS3Image($request->image, 'images/colleges') : '';

        College::create($form_data);

        return response()->json(['message' => __('College Created Successfully')]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(College $college)
    {
        return response()->json(['college' => $college]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCollegeRequest $request, College $college)
    {
        $form_data = $request->validated();
        //image uploading
        if ($request->image) {
            $college->image !=  'assets/images/default.png' ? $this->deleteS3Image($college->image) : '';
            $form_data['image'] = $this->uploadS3Image($request->image, 'images/colleges');
        } else {
            $form_data['image'] = $college->image;
        }
        $college->update($form_data);

        return response()->json(['message' => __('College Updated Successfully')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(College $college)
    {
        $college->image != 'assets/images/default.png' ? $this->deleteS3Image($college->image) : '';
        $college->delete();

        return response()->json(['message' => __('College Deleted Successfully')], 200);
    }
    public function destroyAll(Request $request)
    {
        $colleges = College::whereIn('id', $request->colleges)->get();
        foreach ($colleges as $college) {
            $college->image != 'assets/images/default.png' ? $this->deleteS3Image($college->image) : '';
            $college->delete();
        }
        return response()->json(['message' => __('Colleges Deleted Successfully')]);
    } //end of destroyAll

}
