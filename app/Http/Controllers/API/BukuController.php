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
use Spatie\Async\Pool;
use App\Services\HelperService;

class BukuController extends BaseController
{
    public function __construct(HelperService $service)
    {
        $this->helperService = $service;
    }

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


    public function update(Request $request, $id)
    {
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
            'deskripsi' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $model = Buku::find($id);

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
    }

    public function destroy(Buku $model)
    {
        $model->delete();
        return $this->sendResponse([], 'Post deleted.');
    }


    public function tambahbuku($id, $count)
    {
        $model = Buku::find($id);
        $item = $model->items->last();
        $lastSerie = $item ? $item->nomorseri : 0;
        if (is_null($model)) {
            return $this->sendError('Post does not exist.');
        }

        $results = [];
        $value = new BukuResource($model);
        for ($i = 0; $i < $count; $i++) {
            $lastSerie++;
            $item = ["jenis_id" => $value->id, "nomorseri" => $lastSerie, "jenis" => "buku", "catatan" => ""];
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
            $uploadedImageResponse = $this->helperService->upload($request, 'covers');
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
        $uploadedImageResponse = $this->helperService->upload($request, "bibliografis");
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
