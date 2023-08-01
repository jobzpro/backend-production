<?php

namespace App\Http\Controllers;

use App\Models\IndustryPhysicalSetting;
use Illuminate\Http\Request;

class IndustryPhysicalSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $industry_physical_settings = IndustryPhysicalSetting::all();

        return response([
            'physical_settings' => $industry_physical_settings,
            'message' => "Success",
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $industry_physical_settings = IndustryPhysicalSetting::where('industry_id',$id)->get();

        return response([
            'physical_settings' => $industry_physical_settings,
            'message' => "Success",
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
