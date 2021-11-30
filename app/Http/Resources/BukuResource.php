<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BukuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
      return [
        'id' => $this->id,
        'judul' => $this->judul,
        'kode' => $this->kode ,
        'edisi' => $this->edisi ,
        'bibliografi' => $this->bibliografi ,
        'penulis' => $this->penulis,
        'kategori' => $this->kategori ,
        'isbn' => $this->isbn ,
        'penerbit' => $this->penerbit ,
        'tahun' => $this->tahun,
        'kota' => $this->kota,
        'deskripsi' => $this->deskripsi ,
        'cover' => $this->cover ,
        'lokasi_id' => $this->lokasi_id ,
        'lokasi' => $this->lokasi ,
        'items' => $this->items ,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
      ];
    }
}
