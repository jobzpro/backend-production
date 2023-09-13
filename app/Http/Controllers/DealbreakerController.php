<?php

namespace App\Http\Controllers;

use App\Models\Dealbreaker;
use App\Models\DealbreakerChoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DealbreakerController extends Controller
{
    public function index($id)
    {
        $dealbreakers = Dealbreaker::with('choices')->get();

        return response([
            'dealbreaker' => $dealbreakers->paginate(10),
            'message' => "Successful."
        ], 200);
    }

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

        if ($request->filled('choices')) {
            // $dealbreakers = explode(',', $request['choices']);
            foreach ($request['choices'] as $choice) {
                // for ($i = 0; $i < sizeof($$dealbreakers); $i++) {
                DealbreakerChoice::create([
                    'dealbreaker_id' => $dealbreaker->id,
                    'choice' => $choice,
                ]);
            }
        }

        $dealbreaker = Dealbreaker::with('choices')->find($dealbreaker->id);

        return response([
            'dealbreaker' => $dealbreaker,
            'message' => "Dealbreaker added successfully."
        ], 200);
    }
}
