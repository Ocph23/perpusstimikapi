<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Anggota;
use App\Http\Resources\AnggotaResource;
use Error;
use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AnggotaController extends BaseController
{
    public function index()
    {
        $model = Anggota::all();
        app()->make("expire");
        return $this->sendResponse(AnggotaResource::collection($model), 'Posts fetched.');
    }

    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'nama' => 'required',
                'tanggal_lahir' => 'required',
                'jenis_kelamin' => 'required',
                'agama' => 'required',
                'jenis' => 'required',
                'kewarganegaraan' => 'required',
                'tempat_lahir' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }
            $model = Anggota::create($input);
            return $this->sendResponse(new AnggotaResource($model), 'Post created.');
        } catch (\Exception $e) {
            $message = $this->getSqlError($e->errorInfo);
            if (!$message) {
                $message = $e->getMessage();
            }
            return $this->sendError($message, [], 400);
        }
    }


    public function show($id)
    {
        $model = Anggota::find($id);
        return $this->sendResponse($model, 'Posts fetched.');
    }


    public function update(Request $request, Anggota $model)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'nama' => 'required',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
            'jenis' => 'required',
            'kewarganegaraan' => 'required',
            'tempat_lahir' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $model->nama = $input['nama'];
        $model->tanggal_lahir = $input['tanggal_lahir'];
        $model->tempat_lahir = $input['tempat_lahir'];
        $model->jenis_kelamin = $input['jenis_kelamin'];
        $model->agama = $input['agama'];
        $model->jenis = $input['jenis'];
        $model->kewarganegaraan = $input['kewarganegaraan'];
        $model->save();

        return $this->sendResponse(new AnggotaResource($model), 'Post updated.');
    }

    public function destroy(Anggota $model)
    {
        $model->delete();
        return $this->sendResponse([], 'Post deleted.');
    }


    public function updateStatus($id)
    {
        try {

            $model = Anggota::where('id', $id)->first();
            if (!$model) {
                throw new Exception("Data Anggota Tidak Ditemukan !");
            }

            if($model->aktif=="tidak")
                $model->aktif="ya";
            else    
                $model->aktif="tidak";
            
            $model->save();
            return $this->sendResponse(new AnggotaResource($model), 'Post updated.');
        } catch (\Throwable $e) {
            $message = $this->getSqlError($e->errorInfo);
            if (!$message) {
                $message = $e->getMessage();
            }
            return $this->sendError($message, [], 400);
        }
    }
}
