<?php

namespace App\Providers;

use Aws\S3\S3Client;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Illuminate\Support\Facades\Storage;

class OciObjectStorageServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('oci', function ($app) {
            $config = [
                'credentials' => [
                    'key' => env('OCI_ACCESS_KEY_ID'),
                    'secret' => env('OCI_SECRET_ACCESS_KEY'),
                ],
                'region' => env('OCI_DEFAULT_REGION'),
                'bucket' => env('OCI_BUCKET'),
                'endpoint' => env('OCI_URL'),
                'version' => '2006-03-01',
            ];

            $client = new S3Client($config);

            $adapter = new AwsS3V3Adapter($client, $config['bucket'], '');
            $filesystem = new Filesystem($adapter);

            return $filesystem;
        });
    }
}