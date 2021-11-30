<?php

namespace App\Http\Controllers\API;

use App\Events\DeleteFileEvent;
use Illuminate\Http\Request;
use App\Models\Penelitian;
use App\Models\ItemKarya;
use App\Http\Resources\ItemKaryaResource;
use App\Http\Resources\PenelitianResource;
use App\Libs\HelperService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PenelitianController extends BaseController
{
    public function index()
    {
        $penelitian = Penelitian::all();
        foreach ($penelitian as $key => $value) {
            $value['items']= ItemKaryaResource::collection($value->items);
        }
        return $this->sendResponse( PenelitianResource::collection($penelitian), 'Posts fetched.');
    }
    
    public function store(Request $request)
    {
       try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'kode' => 'required' ,
                'lokasi_id' => 'required' ,
                'npm' => 'required' ,
                'penulis' => 'required',
                'jurusan' => 'required' ,
                'pembimbing' => 'required' ,
                'judul' => 'required',
                'topik' => 'required' ,
                'jenis' => 'required',
                'tahun' => 'required',
                'deskripsi' => 'required' 
            ]);
            if($validator->fails()){
                return $this->sendError("Lengkapi Data Anda !",$validator->errors(), 400);     
            }
            $model = Penelitian::create($input);
            return $this->sendResponse(new PenelitianResource($model), 'Post created.');
       } catch (\Exception $e) {
            $message = $this->errorMessage($e);     
            if(!$message){
                $message = $e->getMessage();
            }
            return $this->sendError($message,[], 400);
       }
    }

    public function show($id)
    {

        $penelitian = Penelitian::find($id);
        if($penelitian)
          {
            $penelitian['items']= ItemKaryaResource::collection($penelitian->items);
            return $this->sendResponse(new PenelitianResource($penelitian), 'Posts fetched.');
        }else{
            return $this->sendResponse($penelitian, 'Posts fetched.');
          }
       
    }
    
    public function update(Request $request, Penelitian $model)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'kode' => 'required' ,
            'lokasi_id' => 'required' ,
            'npm' => 'required' ,
            'penulis' => 'required',
            'jurusan' => 'required' ,
            'pembimbing' => 'required' ,
            'judul' => 'required',
            'topik' => 'required' ,
            'jenis' => 'required',
            'tahun' => 'required',
            'deskripsi' => 'required' ,
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }

        $model = Penelitian::find($input['id'])->first();

        $model->kode = $input['kode']; 
        $model->lokasi_id = $input['lokasi_id']; 
        $model->npm = $input['npm']; 
        $model->penulis = $input['penulis'];
        $model->jurusan = $input['jurusan']; 
        $model->pembimbing = $input['pembimbing']; 
        $model->judul = $input['judul'];
        $model->topik = $input['topik']; 
        $model->jenis = $input['jenis'];
        $model->tahun = $input['tahun'];
        $model->deskripsi = $input['deskripsi']; 
        $model->cover = $input['deskripsi']; 
        $model->save();
        return $this->sendResponse(new PenelitianResource($model), 'Post updated.');
    }
   
    public function destroy(Penelitian $model)
    {
        $model->delete();
        return $this->sendResponse([], 'Post deleted.');
    }


    public function tambahPenelitian($id,$count){
        $model = Penelitian::find($id);
        $item = $model->items->last();
        $lastSerie= $item ? $item->nomorseri : 0;
        if (is_null($model)) {
            return $this->sendError('Post does not exist.');
        }
        
        $results=[];
        $value=new PenelitianResource($model);
        for ($i=0; $i < $count; $i++) { 
            $lastSerie++;
            $item = ["jenis_id"=> $value->id, "nomorseri"=> $lastSerie, "jenis"=> "penelitian", "catatan"=> ""];
            $results[]=ItemKarya::create($item);
        }
        return $this->sendResponse(ItemKaryaResource::collection($results), 'Post fetched.');
    }

    
    public function uploadCover(Request $request, $id)
    {
        try {
            $input = $request->all();
        $uploadedImageResponse = HelperService::upload($request, 'covers');
        $model = Penelitian::find($id);
        $oldFile = $model->cover;
        $model->cover=$uploadedImageResponse["image_name"];
        $model->save();
        if($oldFile){
            event(new DeleteFileEvent('covers/'.$oldFile));
        }
        return $this->sendResponse($uploadedImageResponse, 'success',   200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

}
