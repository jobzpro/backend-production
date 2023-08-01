<?php

namespace App\Http\Controllers;

use App\Models\IndustrySpeciality;
use Illuminate\Http\Request;

class IndustrySpecialitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $industry_speciality = IndustrySpeciality::all();

       return response([
        'industry_speciality' => $industry_speciality,
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
        $industry_speciality = IndustrySpeciality::where('industry_id', $id)->get();

        return response([
            'industry_speciality' => $industry_speciality,
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
