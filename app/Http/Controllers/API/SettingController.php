<?php
   
namespace App\Http\Controllers\API;

use App\Events\DeleteFileEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Models\Setting;
use App\Models\ItemKarya;
use App\Http\Resources\SettingResource;
use App\Http\Resources\ItemKaryaResource;
use Spatie\Async\Pool;
use App\Services\HelperService;

class SettingController extends BaseController
{
    public function __construct(HelperService $service)
    {
        $this->helperService = $service;
    }

    public function index()
    {
        $Setting = Setting::all();
        return $this->sendResponse(SettingResource::collection($Setting), 'Posts fetched.');
    }
    
    public function store(Request $request)
    {
       try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'lamaSewa' => 'required',
                'denda' => 'required',
            ]);
            if($validator->fails()){
                return $this->sendError("Lengkapi Data Anda !",$validator->errors(), 400);
            }
            $model = Setting::create($input);
            return $this->sendResponse(new SettingResource($model), 'Post created.');
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
      try {
        $Setting = Setting::find($id);
        if($Setting)
           {
            $Setting['items']= ItemKaryaResource::collection($Setting->items);
            return $this->sendResponse(new SettingResource($Setting), 'Posts fetched.');
           }
           return $this->sendResponse($Setting,"Data Tidak Ditemukan !");
      } catch (\Throwable $th) {
        return $this->sendError($th->$th->getMessagge() ,[], 400);
      }
    }

    public function getlast()
    {
      try {
        $Setting = Setting::latest()->first();
        return $this->sendResponse($Setting,"force");
      } catch (\Throwable $th) {
        return $this->sendError($th->$th->getMessagge() ,[], 400);
      }
    }
    

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'lamaSewa' => 'required',
            'denda' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }

        $model = Setting::find($id);

        $model->lamaSewa = $input['lamaSewa'];
        $model->denda = $input['denda'];
        $model->save();
        
        return $this->sendResponse(new SettingResource($model), 'Post updated.');
    }
   
    public function destroy(Setting $model)
    {
        $model->delete();
        return $this->sendResponse([], 'Post deleted.');
    }


    public function tambahSetting($id,$count){
        $model = Setting::find($id);
        $item = $model->items->last();

        $lastSerie= $item ? $item->nomorseri : 0;
        if (is_null($model)) {
            return $this->sendError('Post does not exist.');
        }
        
        $results=[];
        $value=new SettingResource($model);
        for ($i=0; $i < $count; $i++) { 
            $lastSerie++;
            $item = ["jenis_id"=> $value->id, "nomorseri"=> $lastSerie, "jenis"=> "Setting", "catatan"=> ""];
            $results[]=ItemKarya::create($item);
        }
        return $this->sendResponse(ItemKaryaResource::collection($results), 'Post fetched.');
    }



    public function CekKetersediaan(Request $request, $id)
    {
        try {
            $Setting = Setting::find($id);
            if($Setting){
                $items=$Setting->items->where('statuspinjam','tersedia');
                $item =$items->first();
                return $this->sendResponse($item, 'fetched.');
            }

            return $this->sendResponse(null, 'not found');

        } catch (\Throwable $th) {
            return $this->sendError($th->$th->getMessagge() ,[], 400);
        }


    }


    // public function uploadCover(Request $request, $id)
    // {
    //     try {
    //     $input = $request->all();
    //     $uploadedImageResponse =$this->helperService->upload($request, 'covers');
    //     $model = Setting::find($id);
    //     $oldFile = $model->cover;
    //     $model->cover=$uploadedImageResponse["image_name"];
    //     $model->save();


    //     if($oldFile){
    //         event(new DeleteFileEvent('covers/'.$oldFile));
    //     }
    //      return $this->sendResponse($uploadedImageResponse, 'success',   200);
    //     } catch (\Throwable $e) {
    //         $message = $this->errorMessage($e->errorInfo);     
    //         if(!$message){
    //             $message = $e->getMessage();
    //         }
    //         return $this->sendError($message,[], 400);
    //     }
    // }

    // public function uploadBibliografi(Request $request, $id)
    // {
    //     $input = $request->all();
    //     $uploadedImageResponse = $this->helperService->upload($request,"bibliografis");
    //     $model = Setting::find($id);
    //     $oldFile = $model->bibliografi;
    //     $model->bibliografi=$uploadedImageResponse["image_name"];
    //     $model->save();
    //     if($oldFile){
    //         event(new DeleteFileEvent('covers/'.$oldFile));
    //     }
    //     return $this->sendResponse($uploadedImageResponse, 'success',   200);
    // }
}