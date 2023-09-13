<?php

namespace App\Http\Controllers;

use App\Models\Dealbreaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DealbreakerController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'question_type' => 'required',
            'company_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'message' => "Create Dealbreaker Unsuccessful.",
                'errors' => $validator->errors(),
            ], 400);
        }

        $dealbreaker = Dealbreaker::create([
            'question' => $request['question'],
            'question_type' => $request['question_type'],
            'company_id' => $request['company_id'],
        ]);

        return response([
            'dealbreaker' => $dealbreaker,
            'message' => "Dealbreaker added successfully."
        ], 200);
    }
}
