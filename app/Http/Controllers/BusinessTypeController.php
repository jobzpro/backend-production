<?php

namespace App\Http\Controllers;

use App\Models\BusinessType;
use Illuminate\Http\Request;

class BusinessTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $business_types = BusinessType::all();

        return response([
            'business_types' => $business_types->paginate(10),
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
    public function show(BusinessType $businessType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BusinessType $businessType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusinessType $businessType)
    {
        //
    }
}
