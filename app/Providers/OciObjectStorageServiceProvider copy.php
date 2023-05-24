<?php

namespace App\Providers;

use Aws\S3\S3Client;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Illuminate\Support\Facades\Storage;

class OciObjectStorageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (config('filesystems.default') != 'oci') {
            return;
        }
        
        Storage::extend('s3', function($app, $config) {
            $client = new S3Client([
                'credentials' => [
                    'key'    => $config['key'],
                    'secret' => $config['secret'],
                ],
                'region' => $config['region'],
                'version' => 'latest',
                'bucket_endpoint' => true,
                'endpoint' => $config['url']
            ]);

            
            $filesystem = new Filesystem(new AwsS3V3Adapter($client, $config['bucket'], ''));


            return $filesystem;
        });
    }

    public function register()
    {
        //
    }
}