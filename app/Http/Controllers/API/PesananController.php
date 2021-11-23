<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\AnggotaResource;
use DateTime;
use DateInterval;
use App\Models\Pesanan;
use App\Models\ItemKarya;
use App\Http\Resources\PesananResource;
use App\Http\Resources\ItemKaryaResource as ItemKaryaResource;
use App\Models\Anggota;
use App\Models\PesananItem;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PesananController extends BaseController
{
    public function index()
    {
        try {
            $models = Pesanan::all();
            foreach ($models as $key => $value) {
                $value->anggota = new AnggotaResource($value->anggota);
                $value->items = $value->items;
            }
            $result = PesananResource::collection($models);
            return $this->sendResponse(PesananResource::collection($models), 'Posts fetched.');
        } catch (\Throwable $th) {
           $vars = 1;
        }
    }
    public function getmine()
    {
        $id = Auth::user()->id;
        $anggota = Anggota::where("user_id", $id)->first();
        if(!$anggota){
            throw new Exception('Maaf Anda Bukan Anggota !');
        }
        try {
            $data =  $anggota['id'];
            $models = Pesanan::where('anggotaid',$data)->get();
            foreach ($models as $key => $value) {
                $value->anggota = new AnggotaResource($value->anggota);
                $value->items = $value->items;
                foreach($value->items as $k=>$v){
                    $v->itemkarya = $v->karyaitem;
                    $v->itemkarya->parent = $v->itemkarya->parent;
                }    

            }
            return $this->sendResponse(PesananResource::collection($models), 'Posts fetched.');
        } catch (\Throwable $th) {
            $message = $this->errorMessage($th);
            return $this->sendError($message, [], 400);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $userid = Auth::id();
            $anggota = Anggota::where("user_id", $userid)->first();
            $input = $request->all();
            if($anggota){
                $input['anggotaid'] =$anggota->id;
            }
            $validator = Validator::make($input, [
                'Items' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->sendError("Lengkapi Data Anda !", $validator->errors(), 400); 
            }
            $input['kode'] = $this->generateRandomString();
            $input['tanggal'] = new DateTime();

            $model = Pesanan::create($input);
            $items = [];
            foreach ($input['Items'] as $key => $value) {
                $tgl = new DateTime($model->created_at);
                $items[] = PesananItem::create(['karyaitemid' => $value['KaryaItemId'], 'pesananid' => $model->id]);
            }
            $model->anggota = $model->anggota;
            DB::commit();
            return $this->sendResponse(new PesananResource($model), 'Post created.');
        } catch (\Exception $e) {
            DB::rollBack();
            $message = $this->errorMessage($e);
            return $this->sendError($message, [], 400);
        }
    }

    public function show($id)
    {

        $model = Pesanan::find($id);
        if ($model) {
            $model->anggota = $model->anggota;
            $model->items = $model->items;
            foreach ($model->items as $key => $value) {
                $value->karyaitem = $value->karyaitem;
                $value->karyaitem->parent = $value->karyaitem->parent;
            }
        }
        return $this->sendResponse($model == null ? $model : new PesananResource($model), 'Posts fetched.');
    }


    public function update(Request $request, Pesanan $model)
    {
        $input = $request->all();

        $validator = Validator::make($input, []);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $model->title = $input['judul'];
        $model->description = $input['penerbit'];
        $model->save();

        return $this->sendResponse(new PesananResource($model), 'Post updated.');
    }

    public function destroy(Pesanan $model)
    {
        $model->delete();
        return $this->sendResponse([], 'Post deleted.');
    }

    function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
