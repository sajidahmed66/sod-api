<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

function resizeImage($vendorId, $file, $width, $height, $location, $quality = 80) {
    $img = Image::make($file);

    if ($width && $height) {
        $img->resize($width, $height);
    } elseif ($width) {
        $img->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
        });
    } elseif ($height) {
        $img->resize(null, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
    }

    $img->encode('webp', $quality);

    $imagePath = $vendorId.'/'.$location.'/'.Str::uuid().'.webp';

    Storage::put($imagePath, $img);

    return $imagePath;
}
