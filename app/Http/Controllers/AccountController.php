<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\User;
use App\Models\PasswordResetTokens;
use App\Models\Company;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use App\Http\Controllers\MailerController as MailerController;


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

        $account = Account::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            //'name' => $data['first_name'].' '.$data['last_name'],
            'login_type' => "email",
            'created_at' => Carbon::now(),
        ]);

        $account->user()->create([
            'account_id' => $account->id,
            // 'first_name' => $data['first_name'],
            // 'middle_name' => $data['middle_name'],
            // 'last_name' => $data['last_name'],
            'email' => $data['email'],
            'created_at' => Carbon::now(),
        ]);

        event(new Registered($account));

        $result = [
            'user' => $account,
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
            if(Hash::check($data['password'], $account['password'])){
                $token = $account->createToken('API Token')->accessToken;

                $result = [
                    'user' => $account,
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
                'password' => Hash::make("password"),
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
                'password' => Hash::make("password"),
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
                'password' => Hash::make("password"),
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
                'password' => Hash::make("password"),
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
        $user = Account::where('email', '=', $data['email'])->first();

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
        $user = Account::where("email", "=", $email)->first();
        $link = env('FRONT_URL'). '/auth/password-reset/'. $token . '?email=' .urlencode($user->email);

        try{
           (new MailerController)->sendResetPasswordEmail($user, $link);
            return true;
        }catch(\Exception $e){
            return false;
        }
    }

    public function resetPassword(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:accounts,email',
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

        $user = Account::where('email', '=', $data['email'])->first();

        if(!$user){
            return response([
                'message' => 'Email not found',
            ],400);
        }

        $user->password = Hash::make($data['password']);
        $user->update();

       PasswordResetTokens::where('email', '=', $request->email)->delete();

       if((new MailerController)->sendSuccessEmail($tokenData->email)){
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

    public function signUpAsAnEmployeer(Request $request){
        $data = $request->all();

        $company = Company::create([
            "name" => $data['company_name'],
            "address_line" => $data['address_line'],
            "city" => $data['city'],
            "state" => $data['state'],
            //"zip_code" => $data['zip_code'],
            "company_email" => $data['company_email'],
            "business_type_id" => $data['business_type_id'],
            "owner_full_name" => $data['owner_full_name'],
        ]);

        $validator = Validator::make($request->all(),[
            'email' => 'required|unique:accounts',
        ]);

        if($validator->fails()){
            return response([
                'message' => "Company creation Unsuccessful",
                'errors' => $validator->errors()
            ],500);
        }

        $user_password = Str::random(12);

        $account_id = Account::insertGetId([
            'email' => $data['email'],
            'password' => Hash::make($user_password),
            'created_at' => Carbon::now(),
        ]);

        $user = User::create([
            'account_id' => $account_id,
            'first_name' => $data['first_name'],
            //'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'created_at' => Carbon::now(),
        ]);

        $userRole = UserRole::create([
            'user_id' => $user->id,
            'role_id' => 2,
            'designation' => $data['designation'],
        ]);

        if((new MailerController)->sendEmployerSuccessEmail($company, $user, $user_password)){
            return response([
                'message' => "Successful"
            ],200);
       }else{
            return response([
                'message' => "A Network Error occurred. Please try again."
            ],500);
       }
    }

    public function signInAsEmployeer(Request $request){
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
            $userRole = UserRole::where('user_id', "=", $user->id)->first();

            if($userRole->role_id == 2){
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
                    'message' => 'employer account not found'
                ],400);
            }

        }else{
            return response([
                'message' => "Cannot find account",
            ],400);
        }
    }


}
