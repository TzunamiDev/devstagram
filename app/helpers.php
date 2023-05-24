<?php

use Illuminate\Support\Facades\Storage;

if (! function_exists('s3_asset')) {
  function s3_asset($path)
  {
    return Storage::disk(env('FILESYSTEM_DRIVER'))->url($path);
  }
}
