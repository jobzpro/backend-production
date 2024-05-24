<?php

namespace App\Http\Controllers;

use App\Mail\Account2FA;
use App\Models\Account;
use App\Models\AccountOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailables\Attachment;

class AccountOtpController extends Controller
{
    public function create2fa($id)
    {
        $account = Account::find($id);
        if (!$account) {
            return response()->json(['message' => 'Account id not found.'], 400);
        } else if ($account && $account->is_otp == true) {
            $account_otp = AccountOtp::where('account_id', $account->id);
            if ($account_otp->exists()) {
                $account_otp->forceDelete();
            }
            $code = Str::random(6);
            AccountOtp::create([
                'account_id' => $account->id,
                'code' => $code,
            ]);
            $mailData = [
                'code' => $code,
            ];

            Mail::to($account->email)->send(new Account2FA($mailData));
            return response()->json(['message' => 'success, check your email'], 200);
        } else {
            return response()->json(['message' => 'something wrong try again.'], 500);
        }
    }

    public function verfiy2fa(Request $request, $id)
    {
        $account = Account::find($id);

        if (!$account) {
            return response()->json(['message' => 'User not found.'], 404);
        } else if ($account) {
            $isExist = AccountOtp::where('account_id', $account->id)
                ->where('code', $request->code);
            if ($isExist->exists()) {
                $isExist->forceDelete();
                return response()->json(['message' => 'OTP is correct.'], 200);
            } else {
                return response()->json(['message' => 'Incorrect OTP.'], 400);
            }
        } else {
            return response()->json(['message' => 'Something wrong please try again'], 500);
        }
    }
}
