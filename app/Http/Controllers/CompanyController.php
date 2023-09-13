<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\StaffInvite;
use App\Models\UserCompany;
use Illuminate\Support\Str;
use App\Http\Controllers\EmployerMailerController as EmployerMailerController;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UploadController as Uploader;
use App\Models\Account;
use App\Models\UserRole;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class CompanyController extends Controller
{

    public function index()
    {
        $companies = Company::with('businessType', 'industry')->get();

        return response([
            'company' => $companies->paginate(10),
            'message' => 'Successful'
        ], 200);
    }

    public function show($id)
    {
        $company = Company::with('userCompany', 'businessType', 'industry')->where('id', $id)->first();

        return response([
            'company' => $company,
            'message' => 'Successful'
        ], 200);
    }


    public function updateBasicDetails(Request $request, $id)
    {
        $company = Company::with('userCompany', 'businessType', 'industry')->where('id', $id)->first();
        $data =  $request->all();

        $imageValidator = Validator::make($request->all(), [
            'company_logo_path' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($imageValidator->fails()) {
            return response([
                'message' => "Invalid file.",
                'errors' => $imageValidator->errors(),
            ], 400);
        } else {
            $company_logo = (new Uploader)->uploadLogo($request->file('company_logo_path'));
        }

        $company->update([
            "business_capacity" => $data['business_capacity'],
            "owner_contact_no" => $data["owner_contact_no"],
            'company_logo_path' => $company_logo,
        ]);

        return response([
            'company' => $company,
            'message' => "Successful"
        ], 200);
    }

    public function updateAdminDetails(Request $request, $id)
    {
        $company = Company::with('userCompany', 'businessType', 'industry')->where('id', $id)->first();

        if ($company->userCompany) {
            $userCompany = $company->userCompany()->whereHas('user.userRoles', function (Builder $query) {
                $query->where('role_id', '=', 2);
            })->first();
            $user = $userCompany->user->update([
                "first_name" => $request['first_name'],
                "last_name" => $request['last_name'],
            ]);

            return response([
                'company' => $company,
                'message' => "Successful"
            ], 200);
        } else {
            return response([
                'message' => 'Admin Details not found',
            ], 400);
        }
    }

    public function updateCompanyDetails(Request $request, $id)
    {
        $company = Company::with('userCompany', 'businessType', 'industry')->where('id', $id)->first();

        $company->update([
            "introduction" => $request['introduction'],
            "services" => $request["services"],
        ]);

        return response([
            'company' => $company,
            'message' => "Successful"
        ], 200);
    }

    public function addEmployerStaff(Request $request, $id)
    {
        $company = Company::with('userCompany', 'businessType', 'industry')->where('id', $id)->first();

        $staffs = $request['staffs'];

        try {
            foreach ($staffs as $staff => $data) {
                if ($data) {
                    $account = Account::create([
                        'email' => $data['email'],
                        'password' => Hash::make(Str::random(12)),
                        'login_type' => "email",
                        'created_at' => Carbon::now(),
                    ]);

                    $account->user()->create([
                        'account_id' => $account->id,
                        'first_name' => $data['first_name'],
                        // 'middle_name' => $data['middle_name'],
                        'last_name' => $data['last_name'],
                        //'email' => $data['email'],
                        'created_at' => Carbon::now(),
                    ]);

                    $userRole = UserRole::create([
                        'user_id' => $account->user->id,
                        'role_id' => 4,
                    ]);

                    $userCompany = UserCompany::create([
                        'user_id' => $account->user->id,
                        'company_id' => $company->id,
                    ]);

                    event(new Registered($account));
                }
            }

            return response([
                'company' => $company,
                'message' => "Successful"
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => 'Something went wrong.',
            ], 400);
        }
    }

    public function destroy(Company $company)
    {
    }


    public function sendStaffinvite(Request $request)
    {
        $data = $request->all();
        $company = Company::find($request->id);
        $account = Auth::user();
        $user = User::find($account->user->id);
        $userCompany = UserCompany::where('user_id', $user->id)->first();


        if ($userCompany->company_id == $request->id) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|unique:accounts|unique:staff_invites',
            ]);

            if ($validator->fails()) {
                return response([
                    'message' => 'Email exists or staff already invited',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $staff_invite = StaffInvite::create([
                'company_id' => $company->id,
                'user_id' => $userCompany->user_id,
                'invite_code' => Str::random(12),
                'email' => $data['email'],
                'invite_expires_at' => Carbon::now()->addHours(72),
            ]);

            $link = env('FRONT_URL') . '/auth/employer/sign-up?invite_code=' . $staff_invite->invite_code . '&company_id=' . $company->id . '&email=' . urlencode($data['email']);

            (new EmployerMailerController)->sendEmployerStaffInvite($company, $data['email'], $user, $link);

            return response([
                'message' => 'User successfully invited.',
            ], 200);
        } else {
            return response([
                'message' => 'Unauthorized'
            ], 400);
        }
    }

    public function resendInvite(Request $request)
    {
        $data = $request->all();
        $staff_invite = StaffInvite::where('email', $data['email'])->first();
        $company = Company::find($staff_invite->company_id);
        $user = User::find($staff_invite->user_id);

        if (!$staff_invite == null) {
            $staff_invite->update([
                'invite_code' => Str::random(12),
                'invite_expires_at' => Carbon::now()->addHours(72),
            ]);

            $link = env('FRONT_URL') . '/auth/employer/sign-up?invite_code=' . $staff_invite->invite_code . '&company_id=' . $company->id . '&email=' . urlencode($data['email']);

            (new EmployerMailerController)->sendEmployerStaffInvite($company, $data['email'], $user, $link);

            return response([
                'message' => 'Invite resent.',
            ], 200);
        } else {
            return response([
                'messsage' => "Email not found",
            ], 400);
        }
    }
}
