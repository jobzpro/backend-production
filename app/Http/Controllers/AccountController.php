<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\User;
use App\Models\PasswordResetTokens;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;

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
            //'name' => $data['first_name'].' '.$data['last_name'],
            'created_at' => Carbon::now(),
        ]);

        $user = User::create([
            'account_id' => $account_id,
            // 'first_name' => $data['first_name'],
            // 'middle_name' => $data['middle_name'],
            // 'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'created_at' => Carbon::now(),
        ]);

        event(new Registered($user));

        $token = $user->createToken('API Token')->accessToken;

        $result = [
            'user' => $user,
            //'token' => $token,
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

    public function redirectToApple(){
        return Socialite::driver('apple')->stateless()->redirect();
    }

    public function handleAppleCallback(){
        try{
            $user = Socialite::driver('apple')->stateless()->user();
        }catch(\Exception $e){
            return redirect('/login');
        }

        $existingAccount = Account::where('email', '=', $user->email)->first();

        if($existingAccount){
            $existingUser = User::where('account_id', $existingAccount->id)->first();
        }else{
            $existingAccount = Account::create([
                'email' => $user->email,
                'name' => $user->name,
                'login_type' => "apple",
                'login_type_id' => $user->id,
                'created_at' => Carbon::now(),
            ]);

            $existingUser = User::where('account_id', $existingAccount->id)->first();
        }

        if($existingUser){
            auth()->login($existingUser,true);

            return response([
                'user' => $existingUser,
                'token' => $user->token,
                'messsage' => 'Sign-in with Apple Successful'
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
                'message' => "Sign-in with Apple Successful"
            ],200);
        }
    }


    public function redirectToLinkedIn(){
        return Socialite::driver('linkedin')->stateless()->redirect();
    }

    public function handleLinkedInCallback(){
        try{
            $user = Socialite::driver('linkedin')->stateless()->user();
        }catch(\Exception $e){
            return redirect('/login');
        }

        $existingAccount = Account::where('email', '=', $user->email)->first();

        if($existingAccount){
            $existingUser = User::where('account_id', $existingAccount->id)->first();
        }else{
            $existingAccount = Account::create([
                'email' => $user->email,
                'name' => $user->name,
                'login_type' => "linkedin",
                'login_type_id' => $user->id,
                'created_at' => Carbon::now(),
            ]);

            $existingUser = User::where('account_id', $existingAccount->id)->first();
        }

        if($existingUser){
            auth()->login($existingUser,true);

            return response([
                'user' => $existingUser,
                'token' => $user->token,
                'messsage' => 'Sign-in with LinkedIn Successful'
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
                'message' => "Sign-in with LinkedIn Successful"
            ],200);
        } 
    }


    public function redirectToFacebook(){
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    public function handleFacebookCallback(){
        try{
            $user = Socialite::driver('facebook')->stateless()->user();
        }catch(\Exception $e){
            return redirect('/login');
        }

        $existingAccount = Account::where('email', '=', $user->email)->first();

        if($existingAccount){
            $existingUser = User::where('account_id', $existingAccount->id)->first();
        }else{
            $existingAccount = Account::create([
                'email' => $user->email,
                'name' => $user->name,
                'login_type' => "facebook",
                'login_type_id' => $user->id,
                'created_at' => Carbon::now(),
            ]);

            $existingUser = User::where('account_id', $existingAccount->id)->first();
        }

        if($existingUser){
            auth()->login($existingUser,true);

            return response([
                'user' => $existingUser,
                'token' => $user->token,
                'messsage' => 'Sign-in with Facebook Successful'
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
                'message' => "Sign-in with Facebook Successful"
            ],200);
        } 
    }

    public function resetPasswordRequest(Request $request){
        
        $data = $request->all();
        $user = User::where('email', '=', $data['email'])->first();

        if(!$user){
            return response([
                'message' => "User doesn't not exist."
            ],400);
        }

        //create password tokens
        PasswordResetTokens::create([
            'email' => $data['email'],
            'token' => Str::random(60),
            'created_at' => Carbon::now()
        ]);

        $tokenData = PasswordResetTokens::where("email", "=", $data['email'])->first();

        if($this->sendResetEmail($data['email'], $tokenData->token)){
            return response([
                'message' => "A reset link has been sent to your email address."
            ],200);
        }else{
            return response([
                'message' => "A network error occured. Please try again."
            ],500);
        }
    }

    public function sendResetEmail($email, $token){
        $user = User::where("email", "=", $email)->select('first_name', 'email')->first();

        $link = env('FRONT_URL'). '/auth/password-reset/'. $token . '?email=' .urlencode($user->email);

        try{
            app('App\Http\Controllers\MailerController')->sendResetPasswordEmail($user, $link);
            return true;
        }catch(\Exception $e){
            return false;
        }
    }

    public function resetPassword(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed',
            'token' => 'required'
        ]);

        if($validator->fails()){
            return response([
                'message' => 'Please complete the details',
                'errors' => $validator->errors(),
            ],400);
        }

        $data = $request->all();
        $tokenData = PasswordResetTokens::where('token', '=', $data['token'])->first();

        if(!$tokenData){
            return response([
                'message' => 'Incorrect token data.'
            ],400);
            
        }

        $user = User::where('email', '=', $data['email'])->first();

        if(!$user){
            return response([
                'message' => 'Email not found',
            ],400);
        }

        $user->password = Hash::make($data['password']);
        $user->update();

       PasswordResetTokens::where('email', '=', $request->email)->delete();

       if(app('App\Http\Controllers\MailerController')->sendSuccessEmail($tokenData->email)){
            return response([
                'message' => "Password Sucessfully changed."
            ],200);
       }else{
            return response([
                'message' => "A Network Error occurred. Please try again."
            ],500);
       }
    }

    public function resetPasswordView(Request $request){
        $token = $request->token;
        $email = urldecode($request->email);
        return response([
            'token' => $token,
            'email' => $email,
            'message' => 'Success'
        ],200);
    }


    

}
