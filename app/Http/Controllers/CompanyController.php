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
use App\Models\Image;
use App\Models\UserRole;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{

    public function index()
    {
        $companies = Company::with('businessType', 'industry')->get();

        return response([
            'company' => $companies,
            'message' => 'Successful'
        ], 200);
    }

    public function show($id)
    {
        $company = Company::with('userCompany.user.account', 'businessType', 'jobListings', 'industry', 'companyReviews.user')->where('id', $id)->first();

        return response([
            'company' => $company,
            'message' => 'Successful'
        ], 200);
    }

    public function uploadCompanyLogo(Request $request, $id)
    {
        $imageValidator = Validator::make($request->all(), [
            'company_logo_path' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);
        if ($imageValidator->fails()) {
            return response([
                'message' => "Invalid file.",
                'errors' => $imageValidator->errors(),
            ], 400);
        } else {
            $company_logo_path =  $this->uploadLogo($request['company_logo_path']);
        }

        $company = Company::find($id);

        if ($company_logo_path == null) {
            $fileName = $company->avatar_path;
        } else {
            $fileName = $company_logo_path->path;
        }

        $company->update([
            'company_logo_path' => $fileName,
        ]);

        return response([
            'company' => $company,
            'message' => 'Upload Success.'
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

        if ($company_logo == null) {
            $fileName = $company->company_logo_path;
        } else {
            $fileName = $company_logo;
        }

        $company->update([
            "business_capacity" => $data['business_capacity'],
            "owner_contact_no" => $data["owner_contact_no"],
            "company_logo_path" => $fileName,
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

        if ($request->filled('introduction')) {
            $company->update([
                "introduction" => $request['introduction'],
            ]);
        }

        if ($request->filled('services')) {
            $company->update([
                "services" => $request["services"],
            ]);
        }

        return response([
            'company' => $company,
            'message' => "Successful"
        ], 200);
    }

    public function updateCompanyBasicDetailSettings(Request $request, $id)
    {
        $company = Company::find($id);
        if (!$company) {
            return response([
                'message' => "Company not found"
            ], 500);
        } else if ($company) {
            $company->update([
                "name" => $request['name'],
                "owner_full_name" => $request['owner_full_name'],
                "owner_contact_no" => $request['owner_contact_no'],
            ]);
            return response([
                'company' => $company,
                'message' => "Successful"
            ], 200);
        } else {
            return response([
                'message' => "somethings wrong try again"
            ], 400);
        }
    }
    public function addEmployerStaff(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:accounts',
        ]);

        if ($validator->fails()) {
            return response([
                'message' => "Add Staff Unsuccessful",
                'errors' => $validator->errors()
            ], 400);
        }

        $company = Company::with('userCompany.user.account', 'businessType', 'industry')->where('id', $id)->first();

        $data = $request->all();

        try {
            // foreach ($staffs as $staff => $data) {
            // if ($data) {
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
            // }
            // }

            $company = Company::with('userCompany.user.account', 'businessType', 'industry')->where('id', $id)->first();

            return response([
                'company' => $company,
                'message' => "Successful"
            ], 200);
        } catch (Exception $e) {
            return response([
                'error' => $e,
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

    private function uploadLogo($image)
    {
        $path = 'companies';

        //!is_dir($path) && mkdir($path, 0777, true);

        if ($file = $image) {
            //Storage::disk('public')->put($path.$fileName, File::get($file));
            $fileName = time() . $file->getClientOriginalName();
            $filePath = Storage::disk('s3')->put($path, $file);
            $filePath   = Storage::disk('s3')->url($filePath);
            $file_type  = $file->getClientOriginalExtension();
            $fileSize   = $this->fileSize($file);

            $avatar = Image::create([
                'name' => $fileName,
                'type' => $file_type,
                'path' => $filePath,
                'size' => $fileSize,
            ]);

            return $avatar;
        } else {
            return $avatar = null;
        }
    }
}
