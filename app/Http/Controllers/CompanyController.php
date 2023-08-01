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

class CompanyController extends Controller
{
    
    public function index(){
        $companies = Company::all();

        return response([
            'company' => $companies->paginate(10),
            'message' => 'Successful'
        ],200);
    }

    public function show(Company $company){
        return response([
            'company' => $company,
            'message' => 'Successful'
        ],200);
    }


    public function update(Company $company, Request $request){

    }

    public function destroy(Company $company){
        
    }


    public function sendStaffinvite(Request $request){
        $data = $request->all();
        $company = Company::find($request->id);
        $userCompany = UserCompany::where('user_id', $request->user()->id)->first();
        $user = User::find($request->user()->id);

        if($userCompany->company_id == $request->id){
            $validator = Validator::make($request->all(),[
                'email' => 'required|unique:accounts|unique:staff_invites',
            ]);

            if($validator->fails()){
                return response([
                    'message' => 'Email exists or staff already invited',
                    'errors' => $validator->errors(),
                ],400);
            }
            
            $staff_invite = StaffInvite::create([
                'company_id' => $company->id,
                'user_id' => $userCompany->user_id,
                'invite_code' => Str::random(12),
                'email' => $data['email'],
                'invite_expires_at' => Carbon::now()->addHours(72),
            ]);

            $link = env('FRONT_URL'). '/auth/employer/sign-up?invite_code='. $staff_invite->invite_code .'&company_id='. $company->id . '&email=' . urlencode($data['email']);

            (new EmployerMailerController)->sendEmployerStaffInvite($company, $data['email'], $user, $link);

            return response([
                'message' => 'User successfully invited.',
            ],200);

            
        }else{
            return response([
                'message' => 'Unauthorized'
            ],400);
        }


    }

    public function resendInvite(Request $request){
        $data = $request->all();
        $staff_invite = StaffInvite::where('email', $data['email'])->first();
        $company = Company::find($staff_invite->company_id);
        $user = User::find($staff_invite->user_id);

        if(!$staff_invite == null){
            $staff_invite->update([
                'invite_code' => Str::random(12),
                'invite_expires_at' => Carbon::now()->addHours(72),
            ]);

            $link = env('FRONT_URL'). '/auth/employer/sign-up?invite_code='. $staff_invite->invite_code .'&company_id='. $company->id . '&email=' . urlencode($data['email']);

            (new EmployerMailerController)->sendEmployerStaffInvite($company, $data['email'], $user, $link);

            return response([
                'message' => 'Invite resent.',
            ],200);
            
        }else{
            return response([
                'messsage' => "Email not found",
            ],400);
        }

    }


}
