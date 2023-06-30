<?php

namespace App\Helper;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


trait FileManager{
  public function fileSize($file, $precision = 2)
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