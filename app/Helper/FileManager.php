<?php

namespace App\Helper;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


trait FileManager{

  public function file_uploads($file, $path){
    if($file){
      $fileName = time() . $file->getClientOriginalName();
      Storage::disk('public')->put($path.$fileName, File::get($file));
      $file_name = $file->getClientOriginalName();
      $filePath   = $path . $fileName;
      $file_type  = $file->getClientOriginalExtension();

      return $file = [
        'fileName' => $file_name,
        'fileType' => $file_type,
        'filePath' => $filePath,
        'fileSize' => $this->attachmentfileSize($file)
      ];
    }
  }
  public function attachmentfileSize($file, $precision = 2)
  {   
    $size = $file->getSize();

    if ( $size > 0 ) {
      $size = (int) $size;
      $base = log($size) / log(1024);
      $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');
      return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }

    return $size;
  }
}