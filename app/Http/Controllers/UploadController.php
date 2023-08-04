<?php

namespace App\Http\Controllers;

use App\Helper\FileManager;
use Illuminate\Http\Request;
use App\Models\FileAttachment;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;


class UploadController extends Controller
{
    use FileManager;

    public function uploadFile($file_attachments, $user_id){
        $path = 'files';

        if($file = $file_attachments){
            $fileName = time().$file->getClientOriginalName();
            $filePath = Storage::disk('s3')->put($path,$file);
            $filePath   = Storage::disk('s3')->url($filePath);
            $file_type  = $file->getClientOriginalExtension();
            $fileSize   = $this->fileSize($file);


            $file_attachments = FileAttachment::create([
                'user_id' => $user_id,
                'name' => $fileName,
                'type' => $file_type,
                'path' => $filePath,
                'size' => $fileSize,
            ]);

            return $file_attachments->path;
        }else{
            return $file_attachments = null;
        }
    }

    public function uploadLogo($company_logo){
        $path = 'company_logo';

        if($file = $company_logo){
            $fileName = time().$file->getClientOriginalName();
            $filePath = Storage::disk('s3')->put($path,$file);
            $filePath   = Storage::disk('s3')->url($filePath);
            $file_type  = $file->getClientOriginalExtension();
            $fileSize   = $this->fileSize($file);

            $company_logo = Image::create([
                'name' => $fileName,
                'type' => $file_type,
                'path' => $filePath,
                'size' => $fileSize,
            ]);

            return $company_logo->path;
        }else{
            return $company_logo = null;
        }
    }
}
