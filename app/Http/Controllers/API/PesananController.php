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
use App\Models\Peminjaman;
use App\Models\PeminjamanItem;
use App\Models\PesananItem;
use App\Models\Setting;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PesananController extends BaseController
{
    public function index()
    {
        try {
            app()->make("expire");
            $models = Pesanan::orderBy('id','Desc')->get();
            foreach ($models as $key => $value) {
                $value->anggota = new AnggotaResource($value->anggota);
                $value->items = $value->items;
            }
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

                $pesananx = Pesanan::
                where("anggotaid", $anggota->id)
                ->where("status", 'baru')
                ->first()  ;
                if($pesananx)
                    throw new Exception("Masih Ada Pesanan Anda Yang Belum Di Proses !");

                $pemijamanx = Peminjaman::
                where("anggotaid", $anggota->id)
                ->where("status", 'sukses')
                ->first()  ;

                if($pemijamanx)
                    throw new Exception("Masih Ada Buku/Penelitian  Yang Belum Anda Kembalikan !");

                $input['anggotaid'] =$anggota->id;
            }
            $validator = Validator::make($input, [
                'items' => 'required'
            ]);
            if ($validator->fails()) {
                throw new Exception("Validator ".   $validator->errors());
            }
            $input['kode'] = $this->generateRandomString();
            $input['tanggal'] = new DateTime();
            $input['status'] = 'baru';
            $model = Pesanan::create($input);
            $items = [];
            foreach ($input['items'] as $key => $value) {
                $tgl = new DateTime($model->created_at);
                $karyaItemId=$value['karyaItemId'];
                $karyaItem = ItemKarya::find($karyaItemId);
                if(!$karyaItem){
                    throw new Exception($karyaItem->nomorseri.  "Tidak Ditemukan !");
                }
                if($karyaItem && $karyaItem->statuspinjam!='tersedia'){
                    throw new Exception($karyaItem->nomorseri." Tidak Tersedia/ Sedang Dipinjam !");
                }
                $items[] = PesananItem::create(['karyaitemid' => $karyaItemId, 'pesananid' => $model->id]);
                $karyaItem->statuspinjam='dipesan';
                $karyaItem->save();
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


    public function andpeminjaman(Request $request)
    {
        DB::beginTransaction();
        try {

            $userid = Auth::id();
            $input = $request->all();
            $anggota = $input["anggota"];
            if($anggota){

                $pesananx = Pesanan::
                where("anggotaid", $anggota['id'])
                ->where("status", 'baru')
                ->first()  ;
                if($pesananx)
                    throw new Exception("Masih Ada Pesanan Anda Yang Belum Di Proses !");

                $pemijamanx = Peminjaman::
                where("anggotaid", $anggota['id'])
                ->where("status", 'sukses')
                ->first()  ;

                if($pemijamanx)
                    throw new Exception("Masih Ada Buku/Penelitian  Yang Belum  Dikembalikan !");

                $input['anggotaid'] =$anggota['id'];
            }
            $validator = Validator::make($input, [
                'items' => 'required'
            ]);
            if ($validator->fails()) {
                throw new Exception("Validator ".   $validator->errors());
            }
            $input['kode'] = $this->generateRandomString();
            $input['tanggal'] = new DateTime();
            $input['status'] = 'baru';
            $model = Pesanan::create($input);
            $items = [];
            foreach ($input['items'] as $key => $value) {
                $tgl = new DateTime($model->created_at);
                $karyaItemId=$value['karyaItemId'];
                $karyaItem = ItemKarya::find($karyaItemId);
                if(!$karyaItem){
                    throw new Exception($karyaItem->nomorseri.  "Tidak Ditemukan !");
                }
                if($karyaItem && $karyaItem->statuspinjam!='tersedia'){
                    throw new Exception($karyaItem->nomorseri." Tidak Tersedia/ Sedang Dipinjam !");
                }
                $items[] = PesananItem::create(['karyaitemid' => $karyaItemId, 'pesananid' => $model->id]);
                $karyaItem->statuspinjam='dipesan';
                $karyaItem->save();
            }

            ///Create Peminjaman
            
            $pesanan = $model;
            if ($pesanan != null)
                $data['items'] = $pesanan->items;
            else
                throw new Exception('Data Pesanan Tidak Ditemukan !');


            if (!$pesanan->items) {
                throw new Exception('Buku yang ingin dipinjam belum ada !');
            }
            $data["keterangan"] = "";
            $data["status"] = "sukses";
            $data["anggotaid"]=$pesanan["anggotaid"];
            $model1 = Peminjaman::create($data);
            $items = [];
            foreach ($data['items'] as $key => $value) {
                $peraturan = Setting::latest()->first();
                if(!$peraturan){
                    throw new Exception("Pengaturan Belum Tersedia !");
                }
                $tgl = Carbon::now()->addDays($peraturan['lamaSewa']);
                $nn=$value['karyaitemid'];
                $karyaitem = ItemKarya::find($nn);
                if ($karyaitem && $karyaitem->statuspinjam == 'dipesan') {
                    $items[] = PeminjamanItem::create(['karyaitem_id' => $value['karyaitemid'], 'peminjaman_id' => $model1->id, 'tanggal_kembali' => $tgl]);
                    $karyaitem->statuspinjam = 'dipinjam';
                    $karyaitem->save();
                } else {
                    throw new Exception('Buku atau Penelitian Judul ' . $karyaitem->parent->judul . ' tidak Tersedia !');
                }
            }
            $pesanan->status = 'sukses';
            $pesanan->save();

            DB::commit();
            return $this->sendResponse(new PesananResource($model1), 'Post created.');
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
