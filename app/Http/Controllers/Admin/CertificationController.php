<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Certification;
use App\Http\Requests\Admin\Certification\StoreCertificationRequest;
use App\Http\Requests\Admin\Certification\UpdateCertificationRequest;
use App\Models\College;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CertificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $certifications = Certification::with('college','user')->latest()->paginate(10);
        $colleges = College::all();
        $users = User::all();
        return ['certifications' => $certifications,'colleges' => $colleges,'users' => $users];
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCertificationRequest $request)
    {
        $form_data = $request->validated();
        $form_data['start_date'] = Carbon::parse($request->start_date)->format('Y-m-d H:i:s');
        $form_data['end_date'] = Carbon::parse($request->end_date)->format('Y-m-d H:i:s');
        $form_data['valid_until'] = Carbon::parse($request->valid_until)->format('Y-m-d H:i:s');


        Certification::create($form_data);
        return response()->json(['message' => __('Education Created Successfully')]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Certification $certification)
    {
        return response()->json(['certification' => $certification]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCertificationRequest $request, Certification $certification)
    {
        $form_data = $request->validated();
//        dd($form_data);

        $request->college_id == "null"? $form_data['college_id'] = null : $form_data['college_id'];

        $form_data['start_date'] = Carbon::parse($request->start_date);
        $form_data['end_date'] = Carbon::parse($request->end_date);
        $form_data['valid_until'] = Carbon::parse($request->valid_until);
        $certification->update($form_data);
        return response()->json(['message' => __('Certification Updated Successfully')]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Certification $certification)
    {
        $certification->delete();
        return response()->json(['message' => __('Certification Deleted Successfully')]);
    }

    public function destroyAll(Request $request)
    {
        Certification::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => __('Certifications Deleted Successfully')]);
    } // end of destroy all function

}
