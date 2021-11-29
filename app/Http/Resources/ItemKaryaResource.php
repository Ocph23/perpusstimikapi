<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemKaryaResource extends JsonResource
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
            'nomorseri'=>  $this->nomorseri,
            'jenis_id'=> $this->jenis_id,
            'jenis'=> $this->jenis,
            'keadaan'=> $this->keadaan,
            'statuspinjam'=> $this->statuspinjam,
            'catatan'=> $this->catatan,
            'parent'=> $this->parent,
            'updated_at'=> $this->updated_at,
            'created_at'=> $this->created_at,
        ];
    }
}




