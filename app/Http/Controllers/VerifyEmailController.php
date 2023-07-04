<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Auth\Events\Verified;
//use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class VerifyEmailController extends Controller
{
    public function __invoke(Request $request){
        $account = Account::find($request->route('id'));
        
        if($account->hasVerifiedEmail()){
            return redirect(env('FRONT_URL'). '?already-success');
            // return response([
            //     'message' => "Email already verified."
            // ],200);
        }

        if($account->markEmailAsVerified()){
            event(new Verified($account));
        }

        return redirect(env('FRONT_URL').'?success');
        // return response([
        //     'message' => "Sucessful"
        // ],200);
    }


    public function resendEmail(Request $request){
        $request->user()->sendEmailVerificationNotification();

        return response([
            'message' => "Verification has been sent."
        ],200);
    }

}
