<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File; 
use Exception;

class HelperService 
{
    public function upload(Request $request, $uploadFolder){
        try {

            $input = $request->all();
            $validator = Validator::make($input, [
                'image' => 'required|image:jpeg,png,jpg,gif,svg|max:2048'
            ]);
        
            if ($validator->fails()) {
                throw new Exception('Data Tidak Ditemukan !');
            }

            $image = $request->file('image');
            $image_uploaded_path = $image->store($uploadFolder, 'public');
            $uploadedImageResponse = array(
                "image_name" => basename($image_uploaded_path),
                "image_url" => Storage::disk('public')->url($image_uploaded_path),
                "mime" => $image->getClientMimeType()
            );
            return $uploadedImageResponse;

        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    public function pengaturan(){
      return  $data[]=["Denda"=>2000,"LamaPinjam"=>3];
    }
}
