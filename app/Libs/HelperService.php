<?php

namespace App\Libs;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File; 

class HelperService 
{
    public static function upload(Request $request, $uploadFolder){
        try {

            $input = $request->all();
            $validator = Validator::make($input, [
                'image' => 'required|image:jpeg,png,jpg,gif,svg|max:2048'
            ]);
        
            if ($validator->fails()) {
                throw new Exception('Data Tidak Ditemukan !');
            }

            $image = $request->file('image');
            $image_uploaded_path = $image->store($uploadFolder, 'public_uploads');
            $uploadedImageResponse = array(
                "image_name" => basename($image_uploaded_path),
                "image_url" => Storage::disk('public_uploads')->url($image_uploaded_path),
                "mime" => $image->getClientMimeType()
            );
            return $uploadedImageResponse;

        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    public static function pengaturan(){
      return  $data[]=["Denda"=>2000,"LamaPinjam"=>3];
    }
}
