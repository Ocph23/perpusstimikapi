<?php

namespace App\Http\Controllers\API;

use App\Events\DeleteFileEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Models\Buku;
use App\Models\ItemKarya;
use App\Http\Resources\BukuResource;
use App\Http\Resources\ItemKaryaResource;
use App\Libs\HelperService;
use Spatie\Async\Pool;
use Illuminate\Support\Facades\DB;

class BukuController extends BaseController
{
    public function index()
    {
        $buku = Buku::all();
        foreach ($buku as $key => $value) {
            $value['items'] = ItemKaryaResource::collection($value->items);
        }
        return $this->sendResponse(BukuResource::collection($buku), 'Posts fetched.');
    }

    public function store(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'kode' => 'required',
                'judul' => 'required',
                'penerbit' => 'required',
                'penulis' => 'required',
                'kota' => 'required',
                'tahun' => 'required',
                'kategori' => 'required',
                'isbn' => 'required',
                'lokasi_id' => 'required',
                'deskripsi' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError("Lengkapi Data Anda !", $validator->errors(), 400);
            }
            $model = Buku::create($input);
            return $this->sendResponse(new BukuResource($model), 'Post created.');
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
            $buku = Buku::find($id);
            if ($buku) {
                $buku['items'] = ItemKaryaResource::collection($buku->items);
                return $this->sendResponse(new BukuResource($buku), 'Posts fetched.');
            }
            return $this->sendResponse($buku, "Data Tidak Ditemukan !");
        } catch (\Throwable $th) {
            return $this->sendError($th->$th->getMessagge(), [], 400);
        }
    }


    public function itemhistory($id)
    {
        try {

            $result = DB::select("SELECT
            peminjaman_item.karyaitem_id,
            peminjaman_item.tanggal_kembali,
            peminjaman_item.statuskembali,
            peminjaman.created_at,
            anggota.nomor_induk,
            anggota.nama
          FROM
            peminjaman_item
            LEFT JOIN peminjaman ON peminjaman_item.peminjaman_id =
          peminjaman.id
            LEFT JOIN anggota ON peminjaman.anggotaid = anggota.id
              where karyaitem_id=?  
              ORDER BY  peminjaman.created_at DESC", [$id]);
            return $this->sendResponse($result, "Data Tidak Ditemukan !");
        } catch (\Throwable $th) {
            return $this->sendError($th->$th->getMessagge(), [], 400);
        }
    }








    public function update(Request $request, $id)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'kode' => 'required',
                'judul' => 'required',
                'penerbit' => 'required',
                'penulis' => 'required',
                'kota' => 'required',
                'tahun' => 'required',
                'kategori' => 'required',
                'isbn' => 'required',
                'lokasi_id' => 'required',
                'deskripsi' => 'required'
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $model = Buku::find($id);
            $model->lokasi_id = $input['lokasi_id'];
            $model->kode = $input['kode'];
            $model->judul = $input['judul'];
            $model->penerbit = $input['penerbit'];
            $model->penulis = $input['penulis'];
            $model->kota = $input['kota'];
            $model->tahun = $input['tahun'];
            $model->kategori = $input['kategori'];
            $model->isbn = $input['isbn'];
            $model->deskripsi = $input['deskripsi'];
            $model->save();

            return $this->sendResponse(new BukuResource($model), 'Post updated.');
        } catch (\Throwable $e) {
            $message = $this->errorMessage($e);
            if (!$message) {
                $message = $e->getMessage();
            }
            return $this->sendError($message, [], 400);
        }
    }

    public function destroy($id)
    {
        $model = Buku::where('id',$id)->get()->first();
        $model->delete();
        return $this->sendResponse([], 'Post deleted.');
    }


    public function tambahbuku($id, $count)
    {
        $model = Buku::find($id);
        $item = $model->items->count();
        $lastSerie = $item ? $item : 0;
        if (is_null($model)) {
            return $this->sendError('Post does not exist.');
        }

        $results = [];
        $value = new BukuResource($model);
        for ($i = 0; $i < $count; $i++) {
            $lastSerie++;
            $item = ["jenis_id" => $value->id, "nomorseri" => $model->kode . "-" . $lastSerie, "jenis" => "buku", "catatan" => ""];
            $results[] = ItemKarya::create($item);
        }
        return $this->sendResponse(ItemKaryaResource::collection($results), 'Post fetched.');
    }



    public function CekKetersediaan(Request $request, $id)
    {
        try {
            $buku = Buku::find($id);
            if ($buku) {
                $items = $buku->items->where('statuspinjam', 'tersedia');
                $item = $items->first();
                return $this->sendResponse($item, 'fetched.');
            }

            return $this->sendResponse(null, 'not found');
        } catch (\Throwable $th) {
            return $this->sendError($th->$th->getMessagge(), [], 400);
        }
    }


    public function uploadCover(Request $request, $id)
    {
        try {
            $input = $request->all();
            $uploadedImageResponse = HelperService::upload($request, 'covers');
            $model = Buku::find($id);
            $oldFile = $model->cover;
            $model->cover = $uploadedImageResponse["image_name"];
            $model->save();
            if ($oldFile) {
                event(new DeleteFileEvent('covers/' . $oldFile));
            }
            return $this->sendResponse($uploadedImageResponse, 'success',   200);
        } catch (\Throwable $e) {
            $message = $this->errorMessage($e->errorInfo);
            if (!$message) {
                $message = $e->getMessage();
            }
            return $this->sendError($message, [], 400);
        }
    }

    public function uploadBibliografi(Request $request, $id)
    {
        $input = $request->all();
        $uploadedImageResponse = HelperService::upload($request, "bibliografis");
        $model = Buku::find($id);
        $oldFile = $model->bibliografi;
        $model->bibliografi = $uploadedImageResponse["image_name"];
        $model->save();
        if ($oldFile) {
            event(new DeleteFileEvent('bibliografis/' . $oldFile));
        }
        return $this->sendResponse($uploadedImageResponse, 'success',   200);
    }
}
