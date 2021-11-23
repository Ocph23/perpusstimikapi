<?php
   
namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Anggota;
use App\Http\Resources\AnggotaResource;
use Symfony\Component\HttpKernel\Exception\HttpException;
   
class AnggotaController extends BaseController
{
    public function index()
    {
        $model = Anggota::all();
        return $this->sendResponse(AnggotaResource::collection($model), 'Posts fetched.');
   
    }
    
    public function store(Request $request)
    {
       try {
        $input = $request->all();
        $validator = Validator::make($input, [
            'nama' => 'required',
            'tanggal_lahir' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }
        $model = Anggota::create($input);
        return $this->sendResponse(new AnggotaResource($model), 'Post created.');
       } catch (\Exception $e) {
        $message = $this->getSqlError($e->errorInfo);     
        if(!$message){
         $message = $e->getMessage();
        }
       return $this->sendError($message,[], 400);
       }
    }

   
    public function show($id)
    {
        $model =Anggota::find($id);
        return $this->sendResponse($model, 'Posts fetched.');
    }
    

    public function update(Request $request, Anggota $model)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'judul' => 'required',
            'penerbit' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }

        $model->title = $input['judul'];
        $model->description = $input['penerbit'];
        $model->save();
        
        return $this->sendResponse(new AnggotaResource($model), 'Post updated.');
    }
   
    public function destroy(Anggota $model)
    {
        $model->delete();
        return $this->sendResponse([], 'Post deleted.');
    }

}