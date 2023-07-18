<?php

namespace App\Http\Controllers;

use Aws\S3\S3Client;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function loadPdfFromS3(){
        $bucket = env('AWS_BUCKET');
        $key = env('AWS_SECRET_ACCESS_KEY');

        $s3 = new S3Client([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
        ]);

        $params = [
            'Bucket' => $bucket,
            'Key' => $key,
        ];

        $url = $s3->getObjectUrl($bucket, $key);
        return $url;

    }
}
