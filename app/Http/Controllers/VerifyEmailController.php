<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
//use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class VerifyEmailController extends Controller
{
    public function __invoke(Request $request){
        $user = User::find($request->route('id'));

        if($user->hasVerifiedEmail()){
            //return redirect(env('FRONT_URL'). '/email/verify/already-success');
            return response([
                'message' => "Email already verified."
            ],200);
        }

        if($user->markEmailAsVerified()){
            event(new Verified($user));
        }

        //return redirect(env('FRONT_URL').'/email/verify/success');
        return response([
            'message' => "Sucessful"
        ],200);
    }


    public function resendEmail(Request $request){
        $request->user()->sendEmailVerificationNotification();

        return response([
            'message' => "Verification has been sent."
        ],200);
    }

}
