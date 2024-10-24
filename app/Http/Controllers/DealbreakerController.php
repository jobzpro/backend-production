<?php

namespace App\Http\Controllers;

use App\Models\Dealbreaker;
use App\Models\DealbreakerChoice;
use App\Models\JobListDealbreaker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DealbreakerController extends Controller
{
    public function index($id)
    {
        $dealbreakers = Dealbreaker::with('choices')->where('company_id', $id)->get();

        return response([
            'dealbreaker' => $dealbreakers,
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

        // if ($request->filled('choices')) {
        //     // $dealbreakers = explode(',', $request['choices']);
        //     foreach ($request['choices'] as $choice) {
        //         // for ($i = 0; $i < sizeof($$dealbreakers); $i++) {
        //         DealbreakerChoice::create([
        //             'dealbreaker_id' => $dealbreaker->id,
        //             'choice' => $choice,
        //         ]);
        //     }
        // }

        if ($request->filled('choices')) {
            foreach ($request['choices'] as $choiceData) {
                if (isset($choiceData['choice'])) {
                    DealbreakerChoice::create([
                        'dealbreaker_id' => $dealbreaker->id,
                        'choice' => $choiceData['choice'],
                    ]);
                }
            }
        }

        $dealbreaker = Dealbreaker::with('choices')->find($dealbreaker->id);

        return response([
            'dealbreaker' => $dealbreaker,
            'message' => "Dealbreaker added successfully."
        ], 200);
    }

    public function manageDealbreakerAnswerAsCompany(Request $request, string $job_list_id)
    {
        // $validator = Validator::make($request->all(), [
        //     'job_list_dealbreaker' => 'array',
        //     'job_list_dealbreaker.*.job_list_dealbreaker_id' => 'sometimes|integer',
        //     'job_list_dealbreaker.*.job_list_id' => 'required|integer',
        //     'job_list_dealbreaker.*.dealbreaker_id' => 'required|integer',
        //     'job_list_dealbreaker.*.dealbreaker_choice_id' => 'required|integer',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json(['message' => $validator->errors()->first()], 400);
        // }

        foreach ($request['job_list_dealbreaker'] as $dealbreakerData) {
            $id = $dealbreakerData['job_list_dealbreaker_id'];
            $existAnswer = JobListDealbreaker::find($id);

            if ($existAnswer !== null) {
                $existAnswer->update([
                    'dealbreaker_choice_id' => $dealbreakerData['dealbreaker_choice_id'],
                    'required' => false
                ]);
            } else {
                JobListDealbreaker::create([
                    'job_list_id' => $dealbreakerData['job_list_id'],
                    'dealbreaker_id' => $dealbreakerData['dealbreaker_id'],
                    'dealbreaker_choice_id' => $dealbreakerData['dealbreaker_choice_id'],
                    'required' => false
                ]);
            }
        }

        return response()->json([
            'message' => 'Success',
            'job_list_dealbreaker' => $request['job_list_dealbreaker'], // Return the processed data
        ], 200);
    }

    public function removeDealbreakerAnswerAsCompany(Request $request, string $job_list_id)
    {
        foreach ($request['job_list_dealbreaker'] as $dealbreakerData) {
            $id = $dealbreakerData['job_list_dealbreaker_id'];
            $existAnswer = JobListDealbreaker::find($id);

            if ($existAnswer !== null) {
                $existAnswer->forceDelete();
            }
        }

        return response()->json([
            'message' => 'Success',
        ], 200);
    }

    public function getDealbreaker(Request $request, $id, $dealbreaker_id)
    {
        $res = Dealbreaker::with('choices')
            ->where('company_id', $id)
            ->where('id', $dealbreaker_id)
            ->first();
        if ($res) {
            return response([
                'dealbreaker' => $res,
                'message' => "Success."
            ], 200);
        } else {
            return response([
                'dealbreaker' => $res,
                'message' => "Success."
            ], 500);
        }
    }

    public function editDealbreakers(Request $request, $dealbreaker_id)
    {
        $validator = Validator::make($request->all(), [
            'question' => 'required',
            'question_type' => 'required',
            'company_id' => 'required',
            'dealbreaker_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'message' => "Update Dealbreaker Unsuccessful.",
                'errors' => $validator->errors(),
            ], 400);
        }

        // $dealbreaker = Dealbreaker::findOrFail($dealbreaker_id);
        $dealbreaker = Dealbreaker::findOrFail($request['dealbreaker_id']);

        $dealbreaker->update([
            'question' => $request['question'],
            'question_type' => $request['question_type'],
            'company_id' => $request['company_id'],
            'dealbreaker_id' => $request['dealbreaker_id'],
        ]);

        if ($request->filled('choices')) {
            DealbreakerChoice::where('dealbreaker_id', '=', $request['dealbreaker_id'])->update([
                'deleted_at' => Carbon::now()
            ]);
            foreach ($request['choices'] as $choiceData) {
                if (isset($choiceData['choice'])) {
                    DealbreakerChoice::create([
                        'dealbreaker_id' => $request['dealbreaker_id'],
                        'choice' => $choiceData['choice'],
                    ]);
                }
            }
        }

        $res = Dealbreaker::with('choices')->find($request['dealbreaker_id']);

        return response([
            'dealbreaker' => $res,
            'message' => "Dealbreaker edited successfully."
        ], 200);
    }

    public function editDealbreakerChoices(Request $request, $dealbreaker_id)
    {
        $request->validate([
            'dealbreaker_id' => 'required|exists:dealbreakers,id',
            'choices' => 'nullable|array',
            'choices.*.id' => 'nullable|exists:dealbreaker_choices,id',
            'choices.*.choice' => 'required_if:choices.*.id,null|string',
        ]);
        $dealbreakerId = $request->input('dealbreaker_id');
        $choicesData = $request->input('choices', []);

        $idsToDelete = collect($choicesData)->pluck('id')->filter();
        DealbreakerChoice::where('dealbreaker_id', $dealbreakerId)
            ->whereNotIn('id', $idsToDelete)
            ->delete();

        foreach ($choicesData as $choice) {
            if (isset($choice['id'])) {
                $existingChoice = DealbreakerChoice::find($choice['id']);
                if ($existingChoice !== null) {
                    $existingChoice->update([
                        'choice' => $choice['choice'],
                    ]);
                }
            } else {
                DealbreakerChoice::create([
                    'dealbreaker_id' => $dealbreakerId,
                    'choice' => $choice['choice'],
                ]);
            }
        }

        $updatedDealbreaker = Dealbreaker::with('choices')->find($dealbreakerId);

        return response()->json([
            'dealbreaker' => $updatedDealbreaker,
            'message' => 'Dealbreaker choices updated successfully.',
        ], 200);
    }

    public function deleteDealbreakers(Request $request, $dealbreaker_id)
    {
        $dealbreakerID = $request->input('dealbreaker_id');
        if ($request->input('dealbreaker_id')) {
            $dealbreaker = Dealbreaker::findOrFail($dealbreakerID);
            // $dealbreakerChoices = DealbreakerChoice::where('dealbreaker_id', '=', $dealbreakerID);
            $dealbreaker->forceDelete();
            // $dealbreakerChoices->forceDelete();
            return response([
                'message' => "Delete Success"
            ], 200);
        } else {
            return response([
                'message' => "ID not found"
            ], 400);
        }
    }
    public function deleteDealbreakerChoices(Request $request, $dealbreaker_id)
    {
        $dealbreakerID = $request->input('dealbreaker_id');
        if ($request->input('dealbreaker_id')) {
            // $dealbreaker = Dealbreaker::where('dealbreaker_id', '=',$dealbreakerID);
            $dealbreakerChoices = DealbreakerChoice::where('dealbreaker_id', '=', $dealbreakerID);
            // $dealbreaker->forceDelete();
            $dealbreakerChoices->forceDelete();
            return response([
                'message' => "Delete Success"
            ], 200);
        } else {
            return response([
                'message' => "ID not found"
            ], 400);
        }
    }
}
