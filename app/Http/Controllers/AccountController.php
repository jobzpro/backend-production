<?php

namespace App\Http\Controllers;

use App\Helper\FileManager;
use App\Http\Controllers\MailerController as MailerController;
use App\Http\Controllers\UploadController as Uploader;
use App\Models\Account;
use App\Models\Company;
use App\Models\Dealbreaker;
use App\Models\DealbreakerChoice;
use App\Models\PasswordResetTokens;
use App\Models\Role;
use App\Models\StaffInvite;
use App\Models\User;
use App\Models\UserCompany;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AccountController extends Controller
{
  use FileManager;

  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|unique:accounts',
      'password' => 'required|confirmed',
    ]);

    if ($validator->fails()) {
      return response([
        'message' => "Registration Unsuccessful",
        'errors' => $validator->errors(),
      ], 400);
    }

    $data = $request->all();
    // $token = $account->createToken('API Token')->accessToken;

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
      //'email' => $data['email'],
      'created_at' => Carbon::now(),
    ]);
    $token = $account->createToken('API Token')->accessToken;

    $userRole = UserRole::create([
      'user_id' => $account->user->id,
      'role_id' => 3,
    ]);

    event(new Registered($account));

    $result = [
      'account' => $account->with('user'),
      'user_role' => $userRole,
      'message' => "Registration Successful",
      'token' => $token
    ];

    return response()->json($result, 200);
  }

  public function login(Request $request)
  {
    $data = $request->all();

    $validator = Validator::make($request->all(), [
      'email' => ['required'],
      'password' => ['required'],
    ]);

    if ($validator->fails()) {
      return response([
        'message' => "Login Unsuccessful",
        'errors' => $validator->errors(),
      ], 400);
    }

    $data = $request->all();

    $account = Account::with('user')->where('email', '=', $data['email'])->first();

    if ($account) {
      $user = User::where('account_id', $account->id)->first();
      $userRoles = UserRole::with('role')->where('user_id', $user->id)->first();

      if ($userRoles->role_id == 3 && $account->email_verified_at != null) {
        if (Hash::check($data['password'], $account['password'])) {
          $token = $account->createToken('API Token')->accessToken;

          $result = [
            'account' => $account,
            'user_role' => $userRoles,
            'token' => $token,
            'message' => "Login Successful",
          ];

          return response()->json($result, 200);
        } else {
          return response([
            'message' => 'username and password do not match',
          ], 400);
        }
      } else if ($userRoles->role_id == 3 && $account->email_verified_at == null) {
        $result = [
          'account' => $account,
          'user_role' => $userRoles,
          'message' => "Please verify your account by checking your email.",
        ];
        return response()->json($result, 300);
      } else {
        return response([
          'message' => 'Account not found',
        ], 400);
      }
    } else {
      return response([
        'message' => "Cannot find account",
      ], 400);
    }
  }

  public function logout()
  {
    if (Auth::check()) {
      Auth::user()->token()->revoke();
      return response([
        'message' => "User logged out successfully",
      ], 200);
    } else {
      return response([
        'message' => "Something went wrong. Please try again.",
      ], 400);
    }
  }

  public function redirectToGoogle()
  {
    //return Socialite::driver('google')->stateless()->redirect();
  }

  public function handleGoogleCallback(Request $request)
  {
    $token = $request['token'];

    try {
      $user = Socialite::driver('google')->userFromToken($token);
    } catch (\Exception $e) {
      return response([
        "message" => "Something went wrong try again later",
      ], 400);
    }

    $existingAccount = Account::where('email', $user->email)->first();
    if ($existingAccount) {
      $existingUser = User::where('account_id', $existingAccount->id)->first();
      // $userRole = UserRole::create([
      //     'user_id' => $existingUser->id,
      //     'role_id' => 3,
      // ]);
    } else {
      $existingAccount = Account::create([
        'email' => $user->email,
        'name' => $user->name,
        'login_type' => "google",
        'login_type_id' => $user->id,
        'password' => Hash::make("password"),
        'created_at' => Carbon::now(),
        'email_verified_at' => Carbon::now(),
      ]);
      $existingUser = User::where('account_id', $existingAccount->id)->first();
    }

    if ($existingUser) {
      $role = $existingUser->userRoles()->first();

      if ($role->role_id == 3) {
        $token = $existingAccount->createToken('API Token')->accessToken;

        return response([
          'user' => $existingUser,
          'user_role' => $existingUser->userRoles()->first(),
          'token' => $token,
          'message' => "Sign-in with Google Successful",
        ], 200);
      } else {
        return response([
          "message" => "Account already exists.",
        ], 400);
      }
    } else {

      $full_name = explode(" ", $user->name);
      $newUser = User::create([
        'account_id' => $existingAccount->id,
        'first_name' => $full_name[0] ?? "",
        'last_name' => $full_name[1] ?? "",
        'created_at' => Carbon::now(),
      ]);

      $newUser->save();
      $token = $existingAccount->createToken('API Token')->accessToken;

      $userRole = UserRole::create([
        'user_id' => $newUser->id,
        'role_id' => 3,
      ]);

      return response([
        'user' => $newUser,
        'token' => $token,
        'message' => "Sign-in with Google Successful",
      ], 200);
    }
  }

  public function redirectToApple()
  {
    // return Socialite::driver('apple')->stateless()->redirect();
  }

  public function handleAppleCallback(Request $request)
  {
    $token = $request['token'];

    try {
      $user = Socialite::driver('google')->userFromToken($token);
    } catch (\Exception $e) {
      return response([
        "message" => "Something went wrong try again later",
      ], 400);
    }

    $existingAccount = Account::where('email', $user->email)->first();
    if ($existingAccount) {
      $existingUser = User::where('account_id', $existingAccount->id)->first();
      // $userRole = UserRole::create([
      //     'user_id' => $existingUser->id,
      //     'role_id' => 3,
      // ]);
    } else {
      $existingAccount = Account::create([
        'email' => $user->email,
        'name' => $user->name,
        'login_type' => "apple",
        'login_type_id' => $user->id,
        'password' => Hash::make("password"),
        'created_at' => Carbon::now(),
        'email_verified_at' => Carbon::now(),
      ]);
      $existingUser = User::where('account_id', $existingAccount->id)->first();
    }

    if ($existingUser) {
      $token = $existingAccount->createToken('API Token')->accessToken;

      return response([
        'user' => $existingUser,
        'user_role' => $existingUser->userRoles()->first(),
        'token' => $token,
        'message' => "Sign-in with Apple Successful",
      ], 200);
    } else {

      $full_name = explode(" ", $user->name);
      $newUser = User::create([
        'account_id' => $existingAccount->id,
        'first_name' => $full_name[0] ?? "",
        'last_name' => $full_name[1] ?? "",
        'created_at' => Carbon::now(),
      ]);

      $newUser->save();
      $token = $existingAccount->createToken('API Token')->accessToken;

      $userRole = UserRole::create([
        'user_id' => $newUser->id,
        'role_id' => 3,
      ]);

      return response([
        'user' => $newUser,
        'token' => $token,
        'message' => "Sign-in with Apple Successful",
      ], 200);
    }
  }

  public function redirectToLinkedIn()
  {
    // return Socialite::driver('linkedin')->stateless()->redirect();
  }

  public function handleLinkedInCallback(Request $request)
  {
    $token = $request['token'];

    try {
      $user = Socialite::driver('linkedin')->userFromToken($token);
    } catch (\Exception $e) {
      return response([
        "message" => "Something went wrong. Please try again",
      ], 400);
    }

    $existingAccount = Account::where('email', '=', $user->email)->first();

    if ($existingAccount) {
      $existingUser = User::where('account_id', $existingAccount->id)->first();
    } else {
      $existingAccount = Account::create([
        'email' => $user->email,
        'password' => Hash::make("password"),
        'name' => $user->name,
        'login_type' => "linkedin",
        'login_type_id' => $user->id,
        'created_at' => Carbon::now(),
        'email_verified_at' => Carbon::now(),
      ]);

      $existingUser = User::where('account_id', $existingAccount->id)->first();
    }

    if ($existingUser) {
      $role = $existingUser->userRoles()->first();

      if ($role->role_id == 3) {
        $token = $existingAccount->createToken('API Token')->accessToken;

        // $userRole = UserRole::create([
        //     'user_id' => $existingUser->id,
        //     'role_id' => 3,
        // ]);

        return response([
          'user' => $existingUser,
          'user_role' => $existingUser->userRoles()->first(),
          'token' => $token,
          'messsage' => 'Sign-in with LinkedIn Successful',
        ], 200);
      } else {
        return response([
          "message" => "Account already exists.",
        ], 400);
      }
    } else {

      $full_name = explode(" ", $user->name);
      $newUser = User::create([
        'account_id' => $existingAccount->id,
        'first_name' => $full_name[0],
        'last_name' => $full_name[1],
        'created_at' => Carbon::now(),
      ]);

      $newUser->save();
      $token = $existingAccount->createToken('API Token')->accessToken;

      $userRole = UserRole::create([
        'user_id' => $newUser->id,
        'role_id' => 3,
      ]);

      return response([
        'user' => $newUser,
        'token' => $token,
        'message' => "Sign-in with LinkedIn Successful",
      ], 200);
    }
  }

  public function redirectToFacebook()
  {
    //return Socialite::driver('facebook')->stateless()->redirect();
  }

  public function handleFacebookCallback(Request $request)
  {
    $token = $request['token'];

    try {
      $user = Socialite::driver('facebook')->userFromToken($token);
    } catch (\Exception $e) {
      return response([
        "message" => "Something went wrong. Please try again",
      ], 400);
    }

    $existingAccount = Account::where('email', '=', $user->email)->first();

    if ($existingAccount) {
      $existingUser = User::where('account_id', $existingAccount->id)->first();
    } else {
      $existingAccount = Account::create([
        'email' => $user->email,
        'password' => Hash::make("password"),
        'name' => $user->name,
        'login_type' => "linkedin",
        'login_type_id' => $user->id,
        'created_at' => Carbon::now(),
        'email_verified_at' => Carbon::now(),
      ]);

      $existingUser = User::where('account_id', $existingAccount->id)->first();
    }

    if ($existingUser) {
      $role = $existingUser->userRoles()->first();

      if ($role->role_id == 3) {
        $token = $existingAccount->createToken('API Token')->accessToken;

        // $userRole = UserRole::create([
        //     'user_id' => $existingUser->id,
        //     'role_id' => 3,
        // ]);

        return response([
          'user' => $existingUser,
          'user_role' => $existingUser->userRoles()->first(),
          'token' => $token,
          'messsage' => 'Sign-in with Facebook Successful',
        ], 200);
      } else {
        return response([
          "message" => "Account already exists.",
        ], 400);
      }
    } else {

      $full_name = explode(" ", $user->name);
      $newUser = User::create([
        'account_id' => $existingAccount->id,
        'first_name' => $full_name[0],
        'last_name' => $full_name[1],
        'created_at' => Carbon::now(),
      ]);

      $newUser->save();
      $token = $existingAccount->createToken('API Token')->accessToken;

      $userRole = UserRole::create([
        'user_id' => $newUser->id,
        'role_id' => 3,
      ]);

      return response([
        'user' => $newUser,
        'token' => $token,
        'message' => "Sign-in with Facebook Successful",
      ], 200);
    }
  }

  public function resetPasswordRequest(Request $request)
  {
    $data = $request->all();
    $account = Account::where('email', '=', $data['email'])->first();
    $role = $account->user->userRoles->role;

    if ($role->role_name != "Jobseeker") {
      return response([
        'message' => "User does not exist.",
      ], 400);
    } else if ($role->role_name == "Employer Admin") {
      return response([
        'message' => "User does not exist.",
      ], 400);
    } else if ($role->role_name ==  "Employer Staff") {
      return response([
        'message' => "User does not exist.",
      ], 400);
    } else if ($role->role_name == "Jobseeker") {
      //create password tokens
      try {
        PasswordResetTokens::create([
          'email' => $data['email'],
          'token' => Str::random(60),
          'created_at' => Carbon::now(),
        ]);
      } catch (\Exception $e) {
      }

      $tokenData = PasswordResetTokens::where("email", "=", $data['email'])->first();

      if ($this->sendResetEmail($data['email'], $tokenData->token)) {
        return response([
          'message' => "A reset link has been sent to your email address.",
        ], 200);
      } else {
        return response([
          'message' => "A network error occured. Please try again.",
        ], 400);
      }
    } else {
      return response([
        'message' => "A network error occured. Please try again.",
      ], 500);
    }
  }

  public function resetPasswordRequestAsEmployer(Request $request)
  {
    $data = $request->all();
    $account = Account::where('email', '=', $data['email'])->first();
    $role = $account->user->userRoles->role;

    if ($role->role_name != ("Employer Admin" || "Employer Staff")) {
      return response([
        'message' => "User does not exist.",
      ], 400);
    } else if ($role->role_name == "Jobseeker") {
      return response([
        'message' => "User does not exist.",
      ], 400);
    } else if ($role->role_name == ("Employer Admin" || "Employer Staff")) {
      //create password tokens
      try {
        PasswordResetTokens::create([
          'email' => $data['email'],
          'token' => Str::random(60),
          'created_at' => Carbon::now(),
        ]);
      } catch (\Exception $e) {
      }

      $tokenData = PasswordResetTokens::where("email", "=", $data['email'])->first();

      if ($this->sendResetEmail($data['email'], $tokenData->token)) {
        return response([
          'message' => "A reset link has been sent to your email address.",
        ], 200);
      } else {
        return response([
          'message' => "A network error occured. Please try again.",
        ], 400);
      }
    } else {
      return response([
        'message' => "A network error occured. Please try again.",
      ], 500);
    }
  }

  public function sendResetEmail($email, $token)
  {
    $account = Account::where("email", "=", $email)->first();

    if ($account->user->userRoles->first()->role->role_name == "Jobseeker") {
      $link = env('FRONT_URL') . '/change-password?token=' . $token . '&email=' . urlencode($account->email);
    } else {
      $link = env('FRONT_URL') . '/auth/employer/password-change?token=' . $token . '&email=' . urlencode($account->email);
    }

    try {
      (new MailerController)->sendResetPasswordEmail($account, $link);
      return true;
    } catch (\Exception $e) {
      return false;
    }
  }

  public function resetPassword(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email|exists:accounts,email',
      'password' => 'required|confirmed',
      'token' => 'required',
    ]);

    if ($validator->fails()) {
      return response([
        'message' => 'Please complete the details',
        'errors' => $validator->errors(),
      ], 400);
    }

    $data = $request->all();
    $tokenData = PasswordResetTokens::where('token', $data['token'])->first();

    if (!$tokenData) {
      return response([
        'message' => 'Incorrect token data.',
      ], 400);
    }

    $user = Account::where('email', $data['email'])->first();

    if (!$user) {
      return response([
        'message' => 'Email not found',
      ], 400);
    }

    $user->password = Hash::make($data['password']);
    $user->update();

    PasswordResetTokens::where('email', $request->email)->delete();

    if ((new MailerController)->sendSuccessEmail($tokenData->email)) {
      return response([
        'message' => "Password Sucessfully changed.",
      ], 200);
    } else {
      return response([
        'message' => "A Network Error occurred. Please try again.",
      ], 400);
    }
  }

  public function userResetPassword(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'password' => 'required|confirmed',
    ]);

    if ($validator->fails()) {
      return response([
        'message' => 'Please complete the details',
        'errors' => $validator->errors(),
      ], 400);
    }

    $account = Auth::user();
    $user = Account::find($account->user->account_id);

    $data = $request->all();

    if (!$user) {
      return response([
        'message' => 'Invalid token',
      ], 400);
    }

    try {
      $user->password = Hash::make($data['password']);
      $user->update();

      return response([
        'message' => "Password Sucessfully changed.",
      ], 200);
    } catch (\Exception $e) {
      return response([
        'message' => $e,
      ], 400);
    }
  }

  public function resetPasswordView(Request $request)
  {
    $token = $request->token;
    $email = urldecode($request->email);
    return response([
      'token' => $token,
      'email' => $email,
      'message' => 'Success',
    ], 200);
  }

  public function signUpAsAnEmployeer(Request $request)
  {
    $data = $request->all();

    $imageValidator = Validator::make($request->all(), [
      'company_logo' => 'image|mimes:jpeg,png,jpg|max:4000',
    ]);

    if ($imageValidator->fails()) {
      return response([
        'message' => "Invalid file",
        'errors' => $imageValidator->errors(),
      ], 400);
    } else {

      $company_logo = (new Uploader)->uploadLogo($request->file('company_logo'));
    }

    $company = Company::create([
      "name" => $data['company_name'],
      //"address_line" => $data['address_line'],
      //"city" => $data['city'],
      //"state" => $data['state'],
      //"zip_code" => $data['zip_code'],
      "company_email" => $data['company_email'],
      "years_of_operation" => $data["years_of_operation"],
      "business_type_id" => $data['business_type_id'],
      "owner_full_name" => $data['owner_full_name'],
      "owner_contact_no" => $data["owner_contact_no"],
      "referral_code" => $data["referral_code"],
      'industry_id' => $data['industry_id'],
      'company_logo_path' => $company_logo,

    ]);

    // add dealbreakers during registration
    if ($company) {
      $dealbreakerDefault = [
        [
          'question' => 'Are you authorized to work in the US?',
          'question_type' => 'Multiple Choice',
          'choices' => [
            ['choice' => 'Yes'],
            ['choice' => 'No'],
          ],
        ],
        [
          'question' => 'Are you vaccinated with Booster?',
          'question_type' => 'Multiple Choice',
          'choices' => [
            ['choice' => 'Vaccinated with Booster'],
            ['choice' => 'Vaccinated without Booster'],
            ['choice' => 'Not vaccinated at all'],
          ],
        ],
        [
          'question' => 'Applicant should be able to',
          'question_type' => 'Multiple Choice',
          'choices' => [
            ['choice' => 'Reliably commute or planning to relocate before starting work'],
            ['choice' => 'Reliably commute or willing to relocate with an employer-provided relocation package'],
          ],
        ],
      ];

      foreach ($dealbreakerDefault as $dealbreakerData) {
        $dealbreakerModel = Dealbreaker::create([
          'question' => $dealbreakerData['question'],
          'question_type' => $dealbreakerData['question_type'],
          'default' => false,
          'company_id' => $company->id,
        ]);

        foreach ($dealbreakerData['choices'] as $choice) {
          DealbreakerChoice::create([
            'dealbreaker_id' => $dealbreakerModel->id,
            'choice' => $choice['choice'],
            'default' => false,
          ]);
        }
      }
    }


    $validator = Validator::make($request->all(), [
      'email' => 'required|unique:accounts',
    ]);

    if ($validator->fails()) {
      return response([
        'message' => "Company creation Unsuccessful",
        'errors' => $validator->errors(),
      ], 400);
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

    $userCompany = UserCompany::create([
      'user_id' => $user->id,
      'company_id' => $company->id,
      'created_at' => Carbon::now(),
    ]);

    (new AdminMailerController)->newEmployerSignUp();

    if ((new MailerController)->sendEmployerSuccessEmail($company, $user, $user_password)) {
      return response([
        'message' => "Successful",
      ], 200);
    } else {
      return response([
        'message' => "A Network Error occurred. Please try again.",
      ], 400);
    }
  }

  public function signInAsEmployeer(Request $request)
  {
    $data = $request->all();

    $validator = Validator::make($request->all(), [
      'email' => ['required'],
      'password' => ['required'],
    ]);

    if ($validator->fails()) {
      return response([
        'message' => "Login Unsuccessful",
        'errors' => $validator->errors(),
      ], 400);
    }

    $data = $request->all();

    $account = Account::where('email', '=', $data['email'])->first();

    if ($account) {
      $user = User::where('account_id', '=', $account->id)->first();
      $userRole = UserRole::with('role')->where('user_id', "=", $user->id)->first();
      $role = Role::where('id', $userRole->role_id)->first();
      $userCompany = UserCompany::where('user_id', $user->id)->first();
      $incorrect_signin_attempts = 1;

      if ($userRole->role_id == (2 || 4)) {
        $company = Company::where('id', $userCompany->company_id)->first();
        if ($company->status == 'approved') {
          if (Hash::check($data['password'], $account['password'])) {
            $token = $account->createToken('API Token')->accessToken;

            //reset incorrect_attempts back to 0
            $user->update([
              'incorrect_signin_attempts' => null,
            ]);

            $result = [
              'user' => $user,
              'user_role' => $userRole,
              'company' => $company,
              'token' => $token,
              'account' => $account,
              'message' => "Login Successful",
            ];
            return response()->json($result, 200);
          } else {
            if ($user->incorrect_signin_attempts == 4) {
              $company->update([
                'status' => 'disabled',
              ]);

              return response([
                'message' => 'Too many incorrect login attempts. Please contact admin.',
              ], 400);
            } else {
              $user->update([
                'incorrect_signin_attempts' => ($incorrect_signin_attempts++),
              ]);
              return response([
                'message' => 'username and password do not match',
              ], 400);
            }
          }
        } else {
          return response([
            'message' => ' Account not yet verified, please contact admin.',
          ], 400);
        }
      } else {
        return response([
          'message' => 'employer account not found.',
        ], 400);
      }
    } else {
      return response([
        'message' => "Cannot find account",
      ], 400);
    }
  }

  public function signUpAsEmployerStaffViaInvite(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'invite_code' => 'required|exists:staff_invites',
      'email' => 'required|email|unique:accounts,email',
      'password' => 'required|confirmed',
    ]);

    if ($validator->fails()) {
      return response([
        'message' => 'Registration Unsuccessful',
        'errors' => $validator->errors(),
      ], 400);
    }

    $data = $request->all();

    $invite_code = StaffInvite::where('invite_code', $data['invite_code'])->first();

    if (!$invite_code) {
      return response([
        'message' => 'Incorrect invite code.',
      ], 400);
    }

    if ($invite_code->invite_expires_at > Carbon::now()) {
      return response([
        'message' => 'Invite code expired. Please contact invitor for a new code.',
      ], 400);
    }

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
      //'email' => $data['email'],
      'created_at' => Carbon::now(),
    ]);

    $userRole = UserRole::create([
      'user_id' => $account->user->id,
      'role_id' => 4,
    ]);

    $userCompany = UserCompany::create([
      'user_id' => $account->user->id,
      'company_id' => $invite_code->company_id,
    ]);

    StaffInvite::where('email', $request->email)->delete();

    event(new Registered($account));

    $result = [
      'account' => $account,
      'user_role' => $userRole,
      'message' => "Registration Successful",
    ];

    return response()->json($result, 200);
  }

  public function checkifCompanyExists(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|unique:companies',
      'company_email' => 'required|unique:companies',
    ]);

    if ($validator->fails()) {
      return response([
        'message' => "Company creation Unsuccessful",
        'errors' => $validator->errors(),
      ], 400);
    } else {
      return response([
        'message' => "Company creation successful",

      ], 200);
    }
  }

  public function accountDeactivation(Request $request)
  {
    $account = Account::find($request->id);
    if (!$account) {
      return response([
        'message' => "Account not found",
      ], 500);
    } else if ($account) {
      $account->delete();
      return response([
        'message' => "Account Deactivate Successfully",
      ], 200);
    } else {
      return response([
        'message' => "Something wrong please try again.",
      ], 400);
    }
  }
}
