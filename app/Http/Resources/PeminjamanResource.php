<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PeminjamanResource extends JsonResource
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
            'id'=> $this->id,
            'kode'=> "PJM". $this->id,
            'keterangan'=> $this->keterangan,
            'status'=> $this->status,
            'anggota'=> $this->anggota,
            'items'=> $this->items,
            'updated_at'=> $this->updated_at,
            'created_at'=> $this->created_at,
        ];
    }
}




