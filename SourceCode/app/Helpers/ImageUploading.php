<?php

namespace App\Helpers;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageUploading
{
    /**
     * Uploading and image converter.
     *
     * @param  string base64Image
     * @param  string belongsTo
     * @param  string Object_id
     * @param  string imageType
     * @return string imagename
     */


    public static function imageUploading($base64Image, $belongsTo,$object_id,$imageType)
    {
        $pos  = strpos($base64Image, ';');
        $type = explode(':', substr($base64Image, 0, $pos))[1];
        $ext=explode('/',$type);
        $imageName=$imageType.'_'.uniqid(10).'.'.$ext[1];
        $img = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        file_put_contents(public_path('storage/'.$belongsTo.'/'.$object_id).'/'.$imageName,base64_decode($img));
        return $imageName;
    }
}
