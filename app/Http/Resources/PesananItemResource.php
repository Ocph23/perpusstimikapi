<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PesananItemResource extends JsonResource
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
            'pesananid'=> $this->pesananid,
            'updated_at'=> $this->updated_at,
            'created_at'=> $this->created_at,
            'nomorview'=> sprintf('%04d',$this->nomorseri),
        ];
    }
}




