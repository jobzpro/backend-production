<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AccountController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|unique:accounts',
            'password' => 'required|confirmed'
        ]);

        if($validator->fails()){
            return response([
                'message' => "Registration Unsuccessful",
                'errors' => $validator->errors()
            ],400);
        }

        $data = $request->all();

        $account_id = Account::insertGetId([
            'email' => $data['email'],
            'name' => $data['first_name'].' '.$data['last_name'],
            'created_at' => Carbon::now(),
        ]);

        $user = User::create([
            'account_id' => $account_id,
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'created_at' => Carbon::now(),
        ]);

        $token = $user->createToken('API Token')->accessToken;

        $result = [
            'user' => $user,
            'token' => $token,
            'message' => "Registration Successful"
        ];

        return response()->json($result, 200);
    }

    public function login(Request $request){
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'email' => ['required'],
            'password' => ['required'],
        ]);

        if($validator->fails()){
            return response([
                'message' => "Login Unsuccessful",
                'errors' => $validator->errors(),
            ],400);
        }

        $data = $request->all();

        $account = Account::where('email', '=', $data['email'])->first();

        if($account){
            $user = User::where('account_id', '=', $account->id)->first();

            if(Hash::check($data['password'], $user['password'])){
                $token = $user->createToken('API Token')->accessToken;

                $result = [
                    'user' => $user,
                    'token' => $token,
                    'message' => "Login Successful"
                ];

                return response()->json($result, 200);
            }else{
                return response([
                    'message' => 'username and password do not match'
                ],400);
            }
        }else{
            return response([
                'message' => "Cannot find account",
            ],400);
        }

    }

    public function logout(){
        if(Auth::check()){
            Auth::user()->token()->revoke();
            return response([
                'message' => "User logged out successfully"
            ],200);
        }else{
            return response([
                'message' => "Something went wrong. Please try again."
            ], 500);
        }
    }


    public function redirectToGoogle(){
        return Socialite::driver('google')->stateless()->redirect();
    }


    public function handleGoogleCallback(){
        try{
            $user = Socialite::driver('google')->stateless()->user();
        }catch(\Exception $e){
            return redirect('/login');
        }

        $existingAccount = Account::where('email', $user->email)->first();
        if($existingAccount){
            $existingUser = User::where('account_id', $existingAccount->id)->first();

        }else{
            $existingAccount = Account::create([
                'email' => $user->email,
                'name' => $user->name,
                'login_type' => "google",
                'login_type_id' => $user->id,
                'created_at' => Carbon::now(),
            ]);
            $existingUser = User::where('account_id', $existingAccount->id)->first();
        }
       
        if($existingUser){
            auth()->login($existingUser, true);
            
            return response([
                'user' => $existingUser,
                'token' => $user->token,
                'message' => "Sign-in with Google Successful"
            ],200);

        }else{

            $full_name = explode(" ", $user->name);
            $newUser = User::create([
                'account_id' => $existingAccount->id,
                'first_name' => $full_name[0],
                'last_name' => $full_name[1],
                'email' => $user->email,
                'password' => Hash::make("password"),
                'created_at' => Carbon::now(),
            ]);

            $newUser->save();
            auth()->login($newUser, true);

            return response([
                'user' => $newUser,
                'token' => $user->token,
                'message' => "Sign-in with Google Successful"
            ],200);
        }
    }
}
