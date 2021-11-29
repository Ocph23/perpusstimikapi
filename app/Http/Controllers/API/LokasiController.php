<?php

namespace App\Http\Controllers\API;

use App\Events\DeleteFileEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Models\Lokasi;
use App\Models\ItemKarya;
use App\Http\Resources\LokasiResource;
use App\Http\Resources\ItemKaryaResource;
use Spatie\Async\Pool;
use App\Services\HelperService;
use Illuminate\Support\Facades\DB;

class LokasiController extends BaseController
{
    public function __construct(HelperService $service)
    {
        $this->helperService = $service;
    }

    public function index()
    {
        $Lokasi = Lokasi::all();
        return $this->sendResponse(LokasiResource::collection($Lokasi), 'Posts fetched.');
    }

    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'nama' => 'required',
            ]);
            if ($validator->fails()) {
                return $this->sendError("Lengkapi Data Anda !", $validator->errors(), 400);
            }
            $model = Lokasi::create($input);
            return $this->sendResponse(new LokasiResource($model), 'Post created.');
        } catch (\Exception $e) {
            $message = $this->errorMessage($e);
            if (!$message) {
                $message = $e->getMessage();
            }
            return $this->sendError($message, [], 400);
        }
    }

    public function show($id)
    {
        try {
            $Lokasi = Lokasi::find($id);
            return $this->sendResponse($Lokasi, "Data Tidak Ditemukan !");
        } catch (\Throwable $th) {
            return $this->sendError($th->$th->getMessagge(), [], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'nama' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $model = Lokasi::find($id);

        $model->nama = $input['nama'];
        $model->keterangan = $input['keterangan'];
        $model->save();

        return $this->sendResponse(new LokasiResource($model), 'Post updated.');
    }

    public function destroy(Lokasi $model)
    {
        $model->delete();
        return $this->sendResponse([], 'Post deleted.');
    }
}
