<?php

namespace App\Http\Controllers;

use App\Helper\FileManager;
use App\Models\EducationalAttainment;
use App\Models\User;
use App\Models\Image;
use App\Models\FileAttachment;
use App\Models\UserExperience;
use App\Models\UserReference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use FileManager;

    public function showJobseekerProfile($id)
    {
        $result = User::with('references', 'files', 'experiences', 'certifications', 'educational_attainments')->where('id', $id)->first();

        return response([
            'user' => $result,
            'message' => 'Successful'
        ], 200);
    }

    public function updateJobseekerProfile(Request $request, $id)
    {
        $imageValidator = Validator::make($request->all(), [
            'avatar' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($imageValidator->fails()) {
            return response([
                'message' => "Invalid file.",
                'errors' => $imageValidator->errors(),
            ], 400);
        } else {
            $avatar =  $this->uploadAvatar($request['avatar']);
        }

        $attached_file = [];

        if ($request->hasFile('files')) {
            $filesValidator = Validator::make($request->all(), [
                'files.*' => 'mimes:pdf,doc,docx,txt|max:2048',
            ]);

            if ($filesValidator->fails()) {
                return response([
                    'message' => "Invalid file.",
                    'errors' => $filesValidator->errors(),
                ], 400);
            } else {
                $path = 'files';
                //!is_dir($path) && mkdir($path, 0777, true);

                foreach ($request->file('files') as $file) {
                    //Storage::disk('public')->put($path.$fileName, File::get($file));
                    $fileName = time() . $file->getClientOriginalName();
                    $filePath = Storage::disk('s3')->put($path, $file);
                    $filePath   = Storage::disk('s3')->url($filePath);
                    $file_type  = $file->getClientOriginalExtension();
                    $fileSize   = $this->fileSize($file);

                    $x = FileAttachment::create([
                        'name' => $fileName,
                        'user_id' => $id,
                        'path' => $filePath,
                        'type' => $file_type,
                        'size' => $fileSize,
                        'is_certification' => true,
                    ]);

                    array_push($attached_file, $x);
                }
            }
        }

        //dd($attached_file);

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'message' => 'Something is wrong. Please try again.',
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = User::with('references', 'files', 'experiences', 'certifications', 'educational_attainments')->where('id', $id)->first();

        if ($avatar == null) {
            $fileName = $user->avatar_path;
        } else {
            $fileName = $avatar->path;
        }

        $user->update([
            'first_name' => $request['first_name'],
            'middle_name' => $request['middle_name'],
            'last_name' => $request['last_name'],
            'phone_number' => $request['phone_number'],
            'profession' => $request['profession'],
            'address_line' => $request['address_line'],
            'city' => $request['city'],
            'province' => $request['province'],
            'avatar_path' => $fileName,
            'elementary_school' => $request['elementary_school'],
            'high_school' => $request['high_school'],
            'college' => $request['college'],
            'description' => $request['description'],
            // 'certifications' => $request['certifications'],
            'skills' => $request['skills'],
            'gender' => $request['gender'],
        ]);

        return response([
            'user' => $user,
            'message' => 'Profile details successfully updated'
        ], 200);
    }

    public function updateReferences(Request $request, $id)
    {
        $user = User::with('references', 'files', 'experiences', 'certifications', 'educational_attainments')->where('id', $id)->first();

        if ($request['reference_id']) {
            $reference = $user->references()->where('id', '=', $request["reference_id"]);

            if ($reference) {
                $reference->delete();

                return response([
                    'user' => $user,
                    'message' => "User references updated."
                ], 200);
            } else {
                return response([
                    'message' => "Reference not found.",
                ], 400);
            }
        } else {
            $reference = UserReference::create([
                'user_id' => $user->id,
                'name' => $request['name'],
                'phone_number' => $request['phone_number'],
            ]);

            return response([
                'user' => $user,
                'message' => "User references updated."
            ], 200);
        }
    }

    public function updateExperiences(Request $request, $id)
    {
        $user = User::with('references', 'files', 'experiences', 'certifications', 'educational_attainments')->where('id', $id)->first();

        if ($request['experience_id']) {
            $experience = $user->experiences()->where('id', '=', $request["experience_id"]);

            if ($experience) {
                $experience->delete();

                return response([
                    'user' => $user,
                    'message' => "User experiences updated."
                ], 200);
            } else {
                return response([
                    'message' => "Experience not found.",
                ], 400);
            }
        } else {
            if ($request->get('current', 0) === "1") {
                UserExperience::where('current', true)->where('user_id', $id)->update(['current' => false]);
            }

            $experience = UserExperience::create([
                'user_id' => $user->id,
                'company_name' => $request['company_name'],
                'years_worked' => $request['years_worked'],
                'position' => $request['position'],
                'current' => $request['current'],
            ]);

            return response([
                'user' => $user,
                'message' => "User experiences updated."
            ], 200);
        }
    }

    public function updateEducationalAttainments(Request $request, $id)
    {
        $user = User::with('references', 'files', 'experiences', 'certifications', 'educational_attainments')->where('id', $id)->first();

        if ($request['educational_attainment_id']) {
            $attainment = $user->educational_attainments()->where('id', '=', $request["educational_attainment_id"]);

            if ($attainment) {
                $attainment->delete();

                return response([
                    'user' => $user,
                    'message' => "Educational Attainments updated."
                ], 200);
            } else {
                return response([
                    'message' => "Educational Attainment not found.",
                ], 400);
            }
        } else {
            $experience = EducationalAttainment::create([
                'user_id' => $user->id,
                'attainment_level' => $request['attainment_level'],
                'school' => $request['school'],
                'year' => $request['year'],
            ]);

            return response([
                'user' => $user,
                'message' => "Educational Attainments updated."
            ], 200);
        }
    }

    public function updateCertifications(Request $request, $id)
    {
        $user = User::with('references', 'files', 'experiences', 'certifications', 'educational_attainments')->where('id', $id)->first();

        if ($request['certification_id']) {
            $certification = $user->certifications()->where('id', '=', $request["certification_id"]);

            if ($certification) {
                $certification->delete();

                return response([
                    'user' => $user,
                    'message' => "Certifications updated."
                ], 200);
            } else {
                return response([
                    'message' => "Certifications not found.",
                ], 400);
            }
        } else {
            $attached_file = [];

            $filesValidator = Validator::make($request->all(), [
                'files.*' => 'mimes:pdf,doc,docx,txt,jpg,jpeg,png',
            ]);

            // if ($filesValidator->fails()) {
            //     return response([
            //         'message' => "Invalid file.",
            //         'errors' => $filesValidator->errors(),
            //     ], 400);
            // } else {
            $path = 'files';
            //!is_dir($path) && mkdir($path, 0777, true);

            foreach ($request->file('files') as $file) {
                //Storage::disk('public')->put($path.$fileName, File::get($file));
                $fileName = time() . $file->getClientOriginalName();
                $filePath = Storage::disk('s3')->put($path, $file);
                $filePath   = Storage::disk('s3')->url($filePath);
                $file_type  = $file->getClientOriginalExtension();
                $fileSize   = $this->fileSize($file);

                $x = FileAttachment::create([
                    'name' => $fileName,
                    'user_id' => $id,
                    'path' => $filePath,
                    'type' => $file_type,
                    'size' => $fileSize,
                    'is_certification' => true,
                ]);

                array_push($attached_file, $x);
            }
            // }

            return response([
                'user' => $user,
                'message' => 'Certifications successfully updated'
            ], 200);
        }
    }

    public function searchResumes(Request $request)
    {
        $keyword = $request->query('keyword');
        $sortFilter = $request->query('sort');

        $users = User::with('currentExperience');

        if (!empty($keyword)) {
            $users = $users->whereHas('currentExperience', function ($q) use ($keyword) {
                $q->where('position', 'LIKE', '%' . $keyword . '%');
            })
            ->orWhere('first_name', 'LIKE', '%' . $keyword . '%')
            ->orWhere('last_name', 'LIKE', '%' . $keyword . '%');
        }

        if ($sortFilter == "Recent to Oldest") {
            $users = $users->latest();
        } elseif ($sortFilter == "Alphabetical") {
            $users = $users->orderBy('first_name', 'ASC');
        } elseif ($sortFilter == "Profile Completion") {
            $users = $users->orderBy('profile_completion', 'DESC');
        } else {
            $users = $users->orderBy('profile_completion', 'DESC');
        }
        
        $users = $users->get();


        return response([
            'users' => $users->paginate(10),
            'message' => "Success",
        ], 200);
    }

    //Private functions
    private function uploadAvatar($image)
    {
        $path = 'avatars';

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
