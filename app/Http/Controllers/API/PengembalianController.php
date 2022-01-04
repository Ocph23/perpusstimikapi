<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use DateTime;
use DateInterval;
use App\Models\Pengembalian;
use App\Models\ItemKarya;
use App\Models\PengembalianItem;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\AnggotaResource;
use App\Http\Resources\PengembalianResource;
use App\Http\Resources\ItemKaryaResource as ItemKaryaResource;
use App\Http\Resources\PeminjamanResource;
use App\Models\Peminjaman;
use App\Models\PeminjamanItem;
use App\Models\Pesanan;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class PengembalianController extends BaseController
{

   

    public function index()
    {
        $models = Pengembalian::all();
        foreach ($models as $key => $value) {
            $value->peminjaman = new PeminjamanResource($value->peminjaman);
            $value->items = $value->items;
        }
        app()->make("expire");
        return $this->sendResponse(PengembalianResource::collection($models), 'Posts fetched.');
    }

    public function store(Request $request)
    {

        DB::beginTransaction();

        try {

            $input = $request->all();
            $validator = Validator::make($input, [
                //  'judul' => 'required',
                // 'penerbit' => 'required'
            ]);
            if (!$input['items']) {
                throw new Exception('Buku yang ingin dipinjam belum ada !');
            }

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }
            $data=[];
            $data['tanggal']= new DateTime($input['tanggal']);
            $data['peminjamanid']=$input['peminjaman']['id'];
            $model = Pengembalian::create($data);
            $items = [];
            foreach ($input['items'] as $key => $value) {
                $value['pengembalianId']=$model['id'];
                $value['peminjamanItemId']=$value['peminjamanItem']['id'];
                $items[] = PengembalianItem::create($value);
                $karyaitem = ItemKarya::find($value['peminjamanItem']['karyaitem_id']);
                $karyaitem->statuspinjam='tersedia';
                $karyaitem->save();
                $pinjamanItem = PeminjamanItem::find($value['peminjamanItemId']);
                $pinjamanItem->statuskembali="sudah";
                $pinjamanItem->save();
            }

            $peminjaman = Peminjaman::find($data['peminjamanid']);
            if($peminjaman!=null){
               $peminjamanItems=$peminjaman->items;
                if(!$peminjamanItems->where("statuskembali","belum")->count())
                {
                    $peminjaman->status="kembali";
                    $peminjaman->save();
                }
            }
            DB::commit();
            return $this->sendResponse(true, 'Post created.');
        } catch (\Exception $e) {
            DB::rollBack();
            $message = $this->errorMessage($e);
            return $this->sendError($message, [], 400);
        }
    }


    public function show($id)
    {

        $model = Pengembalian::find($id);
        if ($model) {
            $model->peminjaman = new PeminjamanResource($model->peminjaman);
            $model->items = $model->items;
            foreach ($model->items as $key => $value) {
                $result = $value->PeminjamanItem->ItemKarya->parent;
                $a = 1;
            }
        }
        return $this->sendResponse($model == null ? $model : new PengembalianResource($model), 'Posts fetched.');
    }


    public function update(Request $request, Pengembalian $model)
    {
        $input = $request->all();
        $validator = Validator::make($input, []);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $model->title = $input['judul'];
        $model->description = $input['penerbit'];
        $model->save();
        return $this->sendResponse(new PengembalianResource($model), 'Post updated.');
    }

    public function destroy(Pengembalian $model)
    {
        $model->delete();
        return $this->sendResponse([], 'Post deleted.');
    }


    public function byKaryaItemId($id)
    {
        try {
            $dataItem = PengembalianItem::where('karyaitem_id', $id)
                ->where('statuskembali', 'belum')
                ->first();
            if ($dataItem) {
                $model = Pengembalian::where('id', $dataItem['Pengembalian_id'])->first();
                if ($model) {
                    $model->anggota = new AnggotaResource($model->anggota);
                    $model->items = $model->items;
                    foreach ($model->items as $key => $value) {
                        $value->ItemKarya=$value->ItemKarya;
                        $value->ItemKarya->parent=$value->ItemKarya->parent;
                    }
                }
                return $this->sendResponse($model == null ? $model : new PengembalianResource($model), 'Posts fetched.');
            } else {
                throw new Exception('Item Buku tidak ditemukan !');
            }
        } catch (\Throwable $th) {
            $message = $this->errorMessage($th);
            return $this->sendError($message, [], 400);
        }
    }
}
