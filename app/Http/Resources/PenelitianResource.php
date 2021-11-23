<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PenelitianResource extends JsonResource
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
            'kode' => $this->kode ,
            'npm' => $this->npm ,
            'penulis' => $this->penulis,
            'jurusan' => $this->jurusan ,
            'pembimbing' => $this->pembimbing ,
            'judul' => $this->judul,
            'topik' => $this->topik ,
            'jenis' => $this->jenis,
            'tahun' => $this->tahun,
            'deskripsi' => $this->deskripsi ,
            'cover' => $this->cover ,
            'items' => $this->items ,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
          ];
    }
}