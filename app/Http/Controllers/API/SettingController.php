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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class SettingController extends BaseController
{
   

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
            if ($validator->fails()) {
                return $this->sendError("Lengkapi Data Anda !", $validator->errors(), 400);
            }
            $model = Setting::create($input);
            return $this->sendResponse(new SettingResource($model), 'Post created.');
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
            $Setting = Setting::find($id);
            if ($Setting) {
                $Setting['items'] = ItemKaryaResource::collection($Setting->items);
                return $this->sendResponse(new SettingResource($Setting), 'Posts fetched.');
            }
            return $this->sendResponse($Setting, "Data Tidak Ditemukan !");
        } catch (\Throwable $th) {
            return $this->sendError($th->$th->getMessagge(), [], 400);
        }
    }

    public function getlast()
    {
        try {
            $Setting = Setting::latest()->first();
            return $this->sendResponse($Setting, "force");
        } catch (\Throwable $th) {
            return $this->sendError($th->$th->getMessagge(), [], 400);
        }
    }


    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'lamaSewa' => 'required',
            'denda' => 'required',
        ]);

        if ($validator->fails()) {
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


    public function tambahSetting($id, $count)
    {
        $model = Setting::find($id);
        $item = $model->items->last();

        $lastSerie = $item ? $item->nomorseri : 0;
        if (is_null($model)) {
            return $this->sendError('Post does not exist.');
        }

        $results = [];
        $value = new SettingResource($model);
        for ($i = 0; $i < $count; $i++) {
            $lastSerie++;
            $item = ["jenis_id" => $value->id, "nomorseri" => $lastSerie, "jenis" => "Setting", "catatan" => ""];
            $results[] = ItemKarya::create($item);
        }
        return $this->sendResponse(ItemKaryaResource::collection($results), 'Post fetched.');
    }



    public function CekKetersediaan(Request $request, $id)
    {
        try {
            $Setting = Setting::find($id);
            if ($Setting) {
                $items = $Setting->items->where('statuspinjam', 'tersedia');
                $item = $items->first();
                return $this->sendResponse($item, 'fetched.');
            }

            return $this->sendResponse(null, 'not found');
        } catch (\Throwable $th) {
            return $this->sendError($th->$th->getMessagge(), [], 400);
        }
    }


    public function dashboard()
    {
        $result = DB::select("select
      (select count(*) from buku) AS buku,
        (select count(*) from anggota) AS anggota,
        (select count(*) from itemkarya where statuspinjam='dipinjam') AS belumkembali,
      (select count(*) from itemkarya where jenis='buku') AS itembuku,
        (select count(*) from itemkarya where jenis='penelitian') AS itempenelitian,
      (select count(*) from penelitian) AS penelitian;");


        return $this->sendResponse($result[0], 'fetched.');
    }


    public function cover($fileName){
        $path = public_path().'/uploads/covers/'.$fileName;
        return Response::download($path);       
    }

    public function bibliografi($fileName){
        $path = public_path().'/uploads/bibliografis/'.$fileName;
        return Response::download($path); 
    }




}
