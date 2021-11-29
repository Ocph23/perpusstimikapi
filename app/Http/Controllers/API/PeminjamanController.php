<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DateTime;
use DateInterval;
use App\Models\Peminjaman;
use App\Models\ItemKarya;
use App\Models\PeminjamanItem;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\AnggotaResource;
use App\Http\Resources\PeminjamanResource;
use App\Http\Resources\ItemKaryaResource as ItemKaryaResource;
use App\Models\Anggota;
use App\Models\Pesanan;
use App\Services\HelperService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PeminjamanController extends BaseController
{

    public function __construct(HelperService $service)
    {
        $this->helperService = $service;
    }


    public function index()
    {
        $userid = Auth::id();
        $anggota = Anggota::where("user_id", $userid)->first();
        $models = null;
        if ($anggota) {
            $models = Peminjaman::where("anggotaid", $anggota->id)->get();
        } else {
            $models = Peminjaman::all();
        }
        foreach ($models as $key => $value) {
            $value->anggota = new AnggotaResource($value->anggota);
            $value->items = $value->items;

            foreach($value->items as $k=>$v){
                $v->ItemKarya = $v->ItemKarya;
                $v->ItemKarya->parent = $v->ItemKarya->parent;
                $v->ItemKarya = new ItemKaryaResource($v->ItemKarya);

            }         


        }
        return $this->sendResponse(PeminjamanResource::collection($models), 'Posts fetched.');
    }

    public function store(Request $request)
    {

        DB::beginTransaction();

        try {

         
            $input = $request->all();
            $validator = Validator::make($input, [
                 'pesananid' => 'required',
                // 'penerbit' => 'required'
            ]);


            $pesanan = Pesanan::find($input['pesananid']);
            if ($pesanan != null)
                $data['items'] = $pesanan->items;
            else
                throw new Exception('Data Pesanan Tidak Ditemukan !');


            if (!$pesanan->items) {
                throw new Exception('Buku yang ingin dipinjam belum ada !');
            }

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $data["keterangan"] = "";
            $data["status"] = "sukses";
            $data["anggotaid"]=$pesanan["anggotaid"];
            $model = Peminjaman::create($data);
            $items = [];
            foreach ($data['items'] as $key => $value) {
                $peraturan = $this->helperService->pengaturan();
                $tgl = Carbon::now()->addDays($peraturan['LamaPinjam']);
                $karyaitem = ItemKarya::find($value['karyaitemid']);
                if ($karyaitem && $karyaitem->statuspinjam == 'tersedia') {
                    $items[] = PeminjamanItem::create(['karyaitem_id' => $value['karyaitemid'], 'peminjaman_id' => $model->id, 'tanggal_kembali' => $tgl]);
                    $karyaitem->statuspinjam = 'dipinjam';
                    $karyaitem->save();
                } else {
                    $karyaitem->parent = $karyaitem->parent;
                    throw new Exception('Buku atau Penelitian Judul ' . $karyaitem->parent->judul . ' tidak Tersedia !');
                }
            }
            $pesanan->status = 'sukses';
            $pesanan->save();

            DB::commit();
            return $this->sendResponse(new PeminjamanResource($model), 'Post created.');
        } catch (\Exception $e) {
            DB::rollBack();
            $message = $this->errorMessage($e);
            return $this->sendError($message, [], 400);
        }
    }


    public function show($id)
    {

        $model = Peminjaman::find($id);
        if ($model) {
            $model->anggota = $model->anggota;
            $model->items = $model->items;
            foreach ($model->items as $key => $value) {
                $result = $value->ItemKarya->parent;
                $value->ItemKarya = new ItemKaryaResource($value->ItemKarya);
            }
        }
        return $this->sendResponse($model == null ? $model : new PeminjamanResource($model), 'Posts fetched.');
    }


    public function update(Request $request, Peminjaman $model)
    {
        $input = $request->all();

        $validator = Validator::make($input, []);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $model->title = $input['judul'];
        $model->description = $input['penerbit'];
        $model->save();

        return $this->sendResponse(new PeminjamanResource($model), 'Post updated.');
    }

    public function destroy(Peminjaman $model)
    {
        $model->delete();
        return $this->sendResponse([], 'Post deleted.');
    }


    public function byKaryaItemId($id)
    {
        try {
            
            $itemKarya = ItemKarya::where('nomorseri', $id)
                ->first();
            if(!$itemKarya)
                throw new Exception('Item Buku tidak ditemukan !');

            $dataItem = PeminjamanItem::where('karyaitem_id', $itemKarya->id)
                ->where('statuskembali', 'belum')
                ->first();

            if ($dataItem) {
                $model = Peminjaman::where('id', $dataItem['peminjaman_id'])->first();
                if ($model) {
                    $model->anggota = new AnggotaResource($model->anggota);
                    $model->items = $model->items;
                    foreach ($model->items as $key => $value) {
                        $value->ItemKarya = $value->ItemKarya;
                        $value->ItemKarya->parent = $value->ItemKarya->parent;
                    }
                }
                return $this->sendResponse($model == null ? $model : new PeminjamanResource($model), 'Posts fetched.');
            } else {
                throw new Exception('Item Peminjaman  tidak ditemukan !');
            }
        } catch (\Throwable $th) {
            $message = $this->errorMessage($th);
            return $this->sendError($message, [], 400);
        }
    }

}
